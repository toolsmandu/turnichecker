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
            'status' => ['nullable', 'in:processing,completed,cancelled'],
            'similarity_report' => ['nullable', 'file'],
            'ai_report' => ['nullable', 'file'],
            'refund' => ['nullable', 'boolean'],
            'error_note' => ['nullable', 'string', 'max:1000'],
            'admin_action' => ['nullable', 'in:complete,cancel,modify_reports'],
        ]);

        $wasCompleted = $submission->status === 'completed';
        $wasCancelled = $submission->status === 'cancelled';

        if ($request->hasFile('similarity_report')) {
            $similarityReport = $request->file('similarity_report');
            if ($submission->similarity_report_path) {
                Storage::disk('public')->delete($submission->similarity_report_path);
            }
            $submission->similarity_report_path = $similarityReport->store('reports', 'public');
            $submission->similarity_report_original_name = $similarityReport->getClientOriginalName();
        }

        if ($request->hasFile('ai_report')) {
            $aiReport = $request->file('ai_report');
            if ($submission->ai_report_path) {
                Storage::disk('public')->delete($submission->ai_report_path);
            }
            $submission->ai_report_path = $aiReport->store('reports', 'public');
            $submission->ai_report_original_name = $aiReport->getClientOriginalName();
        }

        $action = $data['admin_action'] ?? null;
        $skipEmail = $action === 'modify_reports';
        $newStatus = $data['status'] ?? $submission->status;

        if ($action === 'modify_reports') {
            $newStatus = 'processing';
            $this->deleteReportFilesOnly($submission);
            $submission->error_note = null;
        } else {
            $submission->status = $newStatus;
            if ($newStatus === 'cancelled') {
                $submission->error_note = $data['error_note'] ?? 'Cancelled by admin.';
                // Clear reports on cancel to avoid stale downloads
                if ($submission->similarity_report_path) {
                    Storage::disk('public')->delete($submission->similarity_report_path);
                    $submission->similarity_report_path = null;
                    $submission->similarity_report_original_name = null;
                }
                if ($submission->ai_report_path) {
                    Storage::disk('public')->delete($submission->ai_report_path);
                    $submission->ai_report_path = null;
                    $submission->ai_report_original_name = null;
                }
            } else {
                $submission->error_note = null;
            }
        }

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

        if ($isNowCompleted && ! $wasCompleted && ! $skipEmail) {
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

        if ($newStatus === 'cancelled' && ! $wasCancelled && ! $skipEmail) {
            $customerEmail = $submission->user?->email;
            if ($customerEmail) {
                Mail::send(
                    'emails.submission-cancelled',
                    [
                        'submission' => $submission,
                        'customer' => $submission->user,
                        'dashboardUrl' => url('/dashboard'),
                        'adminMessage' => $submission->error_note,
                    ],
                    function ($mailMessage) use ($customerEmail) {
                        $mailMessage->to($customerEmail)
                            ->subject('Your submission was cancelled');
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

    private function deleteReportFilesOnly(Submission $submission): void
    {
        if ($submission->similarity_report_path) {
            Storage::disk('public')->delete($submission->similarity_report_path);
            $submission->similarity_report_path = null;
            $submission->similarity_report_original_name = null;
        }
        if ($submission->ai_report_path) {
            Storage::disk('public')->delete($submission->ai_report_path);
            $submission->ai_report_path = null;
            $submission->ai_report_original_name = null;
        }
    }
}
