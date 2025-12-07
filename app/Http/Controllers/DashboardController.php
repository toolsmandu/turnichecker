<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\UserPack;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $submissions = Submission::with(['user', 'pack'])
                ->latest()
                ->paginate(20);

            return view('dashboard.admin', compact('submissions'));
        }

        $packs = $user->packs()
            ->with(['pack'])
            ->withCount([
                'submissions as completed_submissions_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->orderByDesc('expires_at')
            ->get();

        // Pick the first non-expired pack with remaining slots (safer than relying on a single query).
        $activePack = $packs->first(function (UserPack $pack) {
            return ! $pack->isExpired() && $pack->remainingSlots() > 0;
        });

        // If the user has a valid pack, auto-mark subscription as active to prevent false blocks.
        if (! $user->subscription_active && $activePack) {
            $user->forceFill(['subscription_active' => true])->save();
        }

        $submissions = $user->submissions()->latest()->paginate(20);
        $lastSubmission = $user->submissions()->latest()->first();
        $cooldownRemaining = 0;
        if ($lastSubmission) {
            $nextAllowed = $lastSubmission->created_at->copy()->addSeconds(30);
            if ($nextAllowed->isFuture()) {
                $cooldownRemaining = now()->diffInSeconds($nextAllowed);
            }
        }

        return view('dashboard.customer', [
            'activePack' => $activePack,
            'effectiveQuotaRemaining' => $activePack?->remainingSlots() ?? 0,
            'submissions' => $submissions,
            'cooldownRemaining' => $cooldownRemaining,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();

        $lastSubmission = $user->submissions()->latest()->first();
        if ($lastSubmission) {
            $nextAllowed = $lastSubmission->created_at->copy()->addSeconds(30);
            if ($nextAllowed->isFuture()) {
                $wait = now()->diffInSeconds($nextAllowed);
                return back()->withErrors(['file' => "Please wait {$wait} more seconds before uploading another file."]);
            }
        }


        $packs = $user->packs()
            ->with(['pack'])
            ->withCount([
                'submissions as completed_submissions_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->orderByDesc('expires_at')
            ->get();

        $pack = $packs->first(function (UserPack $pack) {
            return ! $pack->isExpired() && $pack->remainingSlots() > 0;
        });

        $remainingSlots = $pack?->remainingSlots() ?? 0;

        // Auto-reactivate if a valid pack exists but the flag is stale/inactive.
        if (! $user->subscription_active && $pack) {
            $user->forceFill(['subscription_active' => true])->save();
        }

        if (! $user->subscription_active) {
            return back()->withErrors(['file' => 'Your subscription is inactive. Please contact support or an admin.']);
        }

        if (! $pack || $remainingSlots <= 0) {
            return back()->withErrors(['file' => "You don't have enough credits! Please buy a new plan."]);
        }

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $originalName = $request->file('file')->getClientOriginalName();
        $path = $request->file('file')->storeAs('submissions', $originalName, 'public');

        $nextNumber = (Submission::max('submission_number') ?? 230) + 1;

        $submission = Submission::create([
            'user_id' => $user->id,
            'user_pack_id' => $pack->id,
            'submission_number' => $nextNumber,
            'original_name' => $originalName,
            'file_path' => $path,
            'status' => 'processing',
        ]);

        $adminEmail = env('ADMIN_EMAIL', config('mail.from.address'));
        if ($adminEmail) {
            Mail::send(
                'emails.submission-received',
                [
                    'user' => $user,
                    'originalName' => $originalName,
                    'submissionNumber' => $nextNumber,
                ],
                function ($message) use ($adminEmail) {
                    $message->to($adminEmail)->subject('New submission received from Customer');
                }
            );
        }

        return back()->with('status', 'Reports will be generated within 1-2 hours. You will get an email when completed.');
    }

    public function downloadSimilarity(Request $request, Submission $submission)
    {
        $this->authorizeDownload($request->user(), $submission);

        if (! $submission->similarity_report_path) {
            abort(404);
        }

        $name = $submission->similarity_report_original_name
            ?? basename($submission->similarity_report_path)
            ?? 'similarity_report';

        if (! str_starts_with($name, 'similarity_')) {
            $name = 'similarity_'.$name;
        }

        return Storage::disk('public')->download($submission->similarity_report_path, $name);
    }

    public function downloadAi(Request $request, Submission $submission)
    {
        $this->authorizeDownload($request->user(), $submission);

        if (! $submission->ai_report_path) {
            abort(404);
        }

        $name = $submission->ai_report_original_name
            ?? basename($submission->ai_report_path)
            ?? 'ai_report';

        if (! str_starts_with($name, 'ai_')) {
            $name = 'ai_'.$name;
        }

        return Storage::disk('public')->download($submission->ai_report_path, $name);
    }

    public function downloadOriginal(Request $request, Submission $submission)
    {
        $this->authorizeDownload($request->user(), $submission);

        if (! $submission->file_path) {
            abort(404);
        }

        $name = $submission->original_name;
        return Storage::disk('public')->download($submission->file_path, $name);
    }

    public function purchases(Request $request): View
    {
        $packs = $request->user()->packs()->with('pack')->latest()->get();

        return view('account.purchases', [
            'userPacks' => $packs,
        ]);
    }

    public function customers(): View
    {
        $customers = User::where('role', 'customer')
            ->with(['packs' => function ($query) {
                $query->orderByDesc('expires_at');
            }])
            ->orderBy('email')
            ->paginate(30);

        return view('admin.customers', compact('customers'));
    }

    public function updateQuota(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'quota_remaining' => ['required', 'integer', 'min:0'],
        ]);

        $packs = $user->packs()->orderByDesc('expires_at')->get();
        $pack = $packs->first(function ($pack) {
            return $pack->expires_at && $pack->expires_at->isFuture();
        }) ?? $packs->first();

        if (! $pack) {
            return back()->withErrors(['quota' => 'This customer does not have any pack to update.']);
        }

        $pack->quota_remaining = $data['quota_remaining'];
        $pack->save();

        // Keep subscription active if there is an active pack with quota.
        if ($pack->expires_at && $pack->expires_at->isFuture() && $pack->quota_remaining > 0 && ! $user->subscription_active) {
            $user->forceFill(['subscription_active' => true])->save();
        }

        return back()->with('status', 'Quota updated for '.$user->email.'.');
    }

    public function updateSubscription(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'subscription_active' => ['required', 'boolean'],
        ]);

        $user->subscription_active = (bool) $data['subscription_active'];
        $user->save();

        $statusText = $user->subscription_active ? 'activated' : 'deactivated';

        return back()->with('status', 'Subscription '.$statusText.' for '.$user->email.'.');
    }

    public function destroy(Request $request, Submission $submission): RedirectResponse
    {
        $user = $request->user();
        if ($submission->user_id !== $user->id) {
            abort(403);
        }

        if (! in_array($submission->status, ['completed', 'cancelled'], true)) {
            return back()->withErrors(['status' => 'Files that are still processing cannot be deleted.']);
        }

        $this->deleteFiles($submission);
        $submission->delete();

        return back()->with('status', 'Original File and Report Files Deleted Permanently.');
    }

    private function authorizeDownload($user, Submission $submission): void
    {
        if (! $user->isAdmin() && $submission->user_id !== $user->id) {
            abort(403);
        }
    }

    private function deleteFiles(Submission $submission): void
    {
        if ($submission->file_path) {
            Storage::disk('public')->delete($submission->file_path);
        }
        if ($submission->similarity_report_path) {
            Storage::disk('public')->delete($submission->similarity_report_path);
        }
        if ($submission->ai_report_path) {
            Storage::disk('public')->delete($submission->ai_report_path);
        }
    }
}
