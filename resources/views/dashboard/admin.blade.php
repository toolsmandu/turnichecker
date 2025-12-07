@extends('layouts.panel', ['title' => 'Admin Dashboard'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Submissions</div>
            <p style="color:#5d6b80;margin-top:6px;">Review customer uploads and attach reports.</p>
        </div>
        @php
            $adminInitial = strtoupper(substr(auth()->user()->name ?? auth()->user()->email ?? 'A', 0, 1));
        @endphp
        @php
            $hasProcessing = ($processingCount ?? 0) > 0;
            $processingBg = 'linear-gradient(135deg,#065f46,#10b981)';
            $processingBadgeBg = '#d1fae5';
            $processingBadgeColor = '#065f46';
        @endphp
        <div style="display:flex;gap:10px;align-items:center;">
            @if ($hasProcessing)
                <div style="background:{{ $processingBg }};color:#fff;padding:10px 14px;border-radius:12px;display:flex;align-items:center;gap:10px;box-shadow:0 10px 30px rgba(0,0,0,0.12);">
                    <span style="font-size:0.95rem;color:#d1d5db;">Processing</span>
                    <span style="background:{{ $processingBadgeBg }};color:{{ $processingBadgeColor }};padding:6px 10px;border-radius:999px;font-weight:800;min-width:32px;text-align:center;">{{ $processingCount ?? 0 }}</span>
                </div>
            @endif
            <a class="btn btn-ghost" href="{{ route('admin.packs.index') }}" style="background:#ff80ff;">➕Add Package</a>
            <div style="position:relative;">
                <button id="admin-profile-toggle" type="button" style="width:44px;height:44px;border-radius:999px;border:1px solid #e5e7eb;background:#111827;color:#fff;font-weight:700;display:flex;align-items:center;justify-content:center;cursor:pointer;">{{ $adminInitial }}</button>
                <div id="admin-profile-menu" style="position:absolute;top:calc(100% + 8px);right:0;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.12);padding:8px 0;min-width:200px;display:none;z-index:20;">
                    <a href="{{ route('admin.customers') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:#111827;">👥 Customer List</a>
                    <a href="{{ route('admin.settings.edit') }}" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:#111827;">🏠 Customize Home</a>
                    <form id="admin-profile-logout" action="{{ route('logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" style="width:100%;text-align:left;display:flex;align-items:center;gap:10px;padding:10px 14px;border:0;background:transparent;cursor:pointer;color:#b91c1c;font-size:1rem;">↩ Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="status" style="background:#e9f9f1;border-color:#c7f1dc;color:#1b8d5a;margin-bottom:12px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;text-align:center;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;text-align:center;">Date</th>
                    <th style="padding:10px 6px;text-align:center;">File ID</th>
                    <th style="padding:10px 6px;text-align:center;">Customer</th>
                    <th style="padding:10px 6px;text-align:center;">Status</th>
                    <th style="padding:10px 6px;text-align:center;">Submitted File</th>
                    <th style="padding:10px 6px;text-align:center;">Similarity Report</th>
                    <th style="padding:10px 6px;text-align:center;">AI Report</th>
                    <th style="padding:10px 6px;text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submissions as $submission)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;">{{ $submission->created_at->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;font-weight:700;">{{ $submission->submission_number ?? '—' }}</td>
                        <td style="padding:10px 6px;">
                            <span>{{ $submission->user->email }}</span>
                        </td>
                        <td style="padding:10px 6px;font-weight:700;{{ $submission->status === 'completed' ? 'color:#1b8d5a;' : ($submission->status === 'cancelled' ? 'color:#b91c1c;' : 'color:#d97706;') }}">{{ ucfirst($submission->status) }}</td>
                        <td style="padding:10px 6px;">
                            <a href="{{ route('submissions.download.original', $submission) }}" target="_blank">{{ $submission->original_name }}</a>
                        </td>
                        <td style="padding:10px 6px;">
                            @if ($submission->similarity_report_path)
                                <a href="{{ route('submissions.download.similarity', $submission) }}" target="_blank">Download</a>
                            @endif
                            @if ($submission->status !== 'completed')
                                <input type="file" form="form-{{ $submission->id }}" name="similarity_report" style="font-size:0.95rem;display:block;margin-top:6px;">
                            @endif
                        </td>
                        <td style="padding:10px 6px;">
                            @if ($submission->ai_report_path)
                                <a href="{{ route('submissions.download.ai', $submission) }}" target="_blank">Download</a>
                            @endif
                            @if ($submission->status !== 'completed')
                                <input type="file" form="form-{{ $submission->id }}" name="ai_report" style="font-size:0.95rem;display:block;margin-top:6px;">
                            @endif
                        </td>
                        <td style="padding:10px 6px;">
                            <form id="form-{{ $submission->id }}" action="{{ route('admin.submissions.update', $submission) }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:space-between;min-width:180px;">
                                @csrf
                                <input type="hidden" name="status" value="">
                                <input type="hidden" name="error_note" value="">
                                <button class="btn btn-ghost admin-cancel" type="button" data-form="form-{{ $submission->id }}" style="background:#ffe4e6;color:#b91c1c;order:2;">Cancel</button>
                                <button class="btn btn-primary admin-complete" type="button" data-form="form-{{ $submission->id }}" style="order:1;">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $paginator = $submissions;
    @endphp
    <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div style="color:#4b5563;font-size:0.95rem;">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            to
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            results
        </div>
        @if ($paginator->hasPages())
            <nav style="display:flex;gap:6px;align-items:center;">
                @if ($paginator->onFirstPage())
                    <span style="padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px;color:#9ca3af;background:#f9fafb;cursor:not-allowed;">Prev</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" style="padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;text-decoration:none;color:#111827;">Prev</a>
                @endif

                @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:8px 12px;border-radius:10px;background:#111827;color:#fff;font-weight:700;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;text-decoration:none;color:#111827;">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" style="padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;text-decoration:none;color:#111827;">Next</a>
                @else
                    <span style="padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px;color:#9ca3af;background:#f9fafb;cursor:not-allowed;">Next</span>
                @endif
            </nav>
        @endif
    </div>

    <script>
        document.querySelectorAll('.admin-submission-form, form[id^=\"form-\"]').forEach(form => {
            const statusInput = form.querySelector('input[name=\"status\"]');
            const noteInput = form.querySelector('input[name=\"error_note\"]');
            form.querySelector('.admin-complete')?.addEventListener('click', () => {
                if (statusInput) statusInput.value = 'completed';
                if (noteInput) noteInput.value = '';
                form.submit();
            });

            form.querySelector('.admin-cancel')?.addEventListener('click', () => {
                const reason = prompt('Enter the cancellation reason to send to the customer:');
                if (reason === null) return;
                const trimmed = reason.trim();
                if (!trimmed) {
                    alert('Cancellation reason is required.');
                    return;
                }
                if (statusInput) statusInput.value = 'cancelled';
                if (noteInput) noteInput.value = trimmed;
                form.submit();
            });
        });

        const profileToggle = document.getElementById('admin-profile-toggle');
        const profileMenu = document.getElementById('admin-profile-menu');
        const closeProfileMenu = () => {
            if (profileMenu) profileMenu.style.display = 'none';
        };

        profileToggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!profileMenu) return;
            const isOpen = profileMenu.style.display === 'block';
            profileMenu.style.display = isOpen ? 'none' : 'block';
        });

        document.addEventListener('click', (e) => {
            if (!profileMenu || !profileToggle) return;
            if (profileMenu.contains(e.target) || profileToggle.contains(e.target)) return;
            closeProfileMenu();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeProfileMenu();
            }
        });
    </script>
@endsection
