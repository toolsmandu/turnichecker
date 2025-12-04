<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SubmissionAdminController extends Controller
{
    public function index(): View
    {
        $submissions = Submission::with(['user', 'pack'])
            ->latest()
            ->paginate(20);

        return view('admin.submissions', compact('submissions'));
    }

    public function update(Request $request, Submission $submission): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:processing,completed'],
            'similarity_report' => ['nullable', 'file'],
            'ai_report' => ['nullable', 'file'],
            'refund' => ['nullable', 'boolean'],
        ]);

        $wasCompleted = $submission->status === 'completed';

        if ($request->hasFile('similarity_report')) {
            if ($submission->similarity_report_path) {
                Storage::disk('public')->delete($submission->similarity_report_path);
            }
            $submission->similarity_report_path = $request->file('similarity_report')->store('reports', 'public');
        }

        if ($request->hasFile('ai_report')) {
            if ($submission->ai_report_path) {
                Storage::disk('public')->delete($submission->ai_report_path);
            }
            $submission->ai_report_path = $request->file('ai_report')->store('reports', 'public');
        }

        $newStatus = $data['status'] ?? $submission->status;
        $submission->status = $newStatus;
        $submission->save();

        $isNowCompleted = $newStatus === 'completed';
        $submission->loadMissing('user');

        if ($isNowCompleted && ! $wasCompleted && $submission->pack && $submission->pack->quota_remaining > 0) {
            $submission->pack->decrement('quota_remaining');
        }

        if ($request->boolean('refund') && $submission->pack) {
            $submission->pack->increment('quota_remaining');
        }

        if ($isNowCompleted && ! $wasCompleted) {
            $customerEmail = $submission->user?->email;
            if ($customerEmail) {
                Mail::send(
                    'emails.submission-completed',
                    [
                        'submission' => $submission,
                        'customer' => $submission->user,
                        'dashboardUrl' => url('/dashboard'),
                    ],
                    function ($message) use ($customerEmail) {
                        $message->to($customerEmail)
                            ->subject('Your submission is completed');
                    }
                );
            }
        }

        return back()->with('status', 'Submission updated.');
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        $this->deleteFiles($submission);
        $submission->delete();

        return back()->with('status', 'Submission and files deleted.');
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
