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

        $activePack = $user->packs()
            ->where('expires_at', '>', now())
            ->orderByDesc('expires_at')
            ->first();

        $submissions = $user->submissions()->latest()->paginate(10);

        return view('dashboard.customer', [
            'activePack' => $activePack,
            'submissions' => $submissions,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user->subscription_active) {
            return back()->withErrors(['file' => 'Your subscription is inactive. Please contact support or an admin.']);
        }

        $lastSubmission = $user->submissions()->latest()->first();
        if ($lastSubmission) {
            $secondsSinceLast = now()->diffInSeconds($lastSubmission->created_at);
            if ($secondsSinceLast < 30) {
                $wait = 30 - $secondsSinceLast;
                return back()->withErrors(['file' => "Please wait {$wait} more seconds before uploading another file."]);
            }
        }

        $pack = $user->packs()
            ->where('expires_at', '>', now())
            ->where('quota_remaining', '>', 0)
            ->orderByDesc('expires_at')
            ->first();

        if (! $pack) {
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

        return back()->with('status', 'Reports are usually generated within 1 hour between 9AM - 9PM Nepal Time. You will get an email when completed or refresh here later.');
    }

    public function downloadSimilarity(Request $request, Submission $submission)
    {
        $this->authorizeDownload($request->user(), $submission);

        if (! $submission->similarity_report_path) {
            abort(404);
        }

        $name = 'similarity_'.$submission->original_name;
        return Storage::disk('public')->download($submission->similarity_report_path, $name);
    }

    public function downloadAi(Request $request, Submission $submission)
    {
        $this->authorizeDownload($request->user(), $submission);

        if (! $submission->ai_report_path) {
            abort(404);
        }

        $name = 'ai_'.$submission->original_name;
        return Storage::disk('public')->download($submission->ai_report_path, $name);
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

        $pack = $user->packs()->orderByDesc('expires_at')->first();

        if (! $pack) {
            return back()->withErrors(['quota' => 'This customer does not have any pack to update.']);
        }

        $pack->quota_remaining = $data['quota_remaining'];
        $pack->save();

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

        if ($submission->status !== 'completed') {
            return back()->withErrors(['status' => 'Files on Processing Status cannot be deleted.']);
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
