@extends('layouts.panel', ['title' => 'Dashboard'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Your Dashboard</div>
            <p style="color:#5d6b80;margin-top:6px;">Upload your file to check similarity and AI without stoting in Turnitin's database.</p>
        </div>
        @php
            $user = auth()->user();
            $initials = strtoupper(substr($user->name ?? $user->email, 0, 1));
        @endphp
        <div style="position:relative;">
            <button id="profile-toggle" type="button" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border:1px solid #dbe1ea;border-radius:999px;background:#f8fafc;cursor:pointer;box-shadow:0 6px 16px rgba(0,0,0,0.07);">
                <span style="width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#4f8bff,#3b76f6);color:#fff;font-weight:800;">{{ $initials }}</span>
                <span style="font-size:12px;">▾</span>
            </button>
            <div id="profile-menu" style="display:none;position:absolute;right:0;top:48px;width:220px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 18px 42px rgba(0,0,0,0.12);padding:10px;z-index:10;">
                <div style="padding:6px 8px;font-weight:800;color:#111827;font-size:0.95rem;word-break:break-word;">{{ $user->email }}</div>
                <hr style="border:none;border-top:1px solid #e5e7eb;margin:8px 0;">
                <a href="{{ route('account.password.edit') }}" style="display:flex;align-items:center;gap:8px;padding:6px 8px;color:#1f2937;text-decoration:none;border-radius:8px;cursor:pointer;">✎ Edit Details</a>
                <a href="{{ route('account.purchases') }}" style="display:flex;align-items:center;gap:8px;padding:6px 8px;color:#1f2937;text-decoration:none;border-radius:8px;cursor:pointer;">🛒 Purchase History</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="width:100%;text-align:left;display:flex;align-items:center;gap:8px;padding:6px 8px;color:#b91c1c;background:none;border:none;border-radius:8px;cursor:pointer;font-size:1rem;">➜] Log out</button>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="status" style="background:#fff4f3;border-color:#f2c8c3;color:#b91c1c;margin-bottom:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div class="status" style="background:#e9f9f1;border-color:#c7f1dc;color:#1b8d5a;margin-bottom:12px;">
            {{ session('status') }}
        </div>
    @endif

    @php
        $remainingSlots = $effectiveQuotaRemaining ?? ($activePack->quota_remaining ?? 0);
        $hasActivePack = $activePack && $remainingSlots > 0 && $activePack->expires_at && $activePack->expires_at->isFuture();
    @endphp
    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <strong>Available Slots:</strong>
            @if ($hasActivePack)
                <span style="padding:8px 12px;background:#e5f2ff;border-radius:10px;font-weight:800;">
                    {{ $remainingSlots }} Uploads
                </span>
            @else
                <span style="padding:8px 12px;background:#fee2e2;border-radius:10px;font-weight:800;color:#b91c1c;">No Active Plan</span>
            @endif
            <strong>Expires:</strong>
            @if ($hasActivePack)
                <span style="padding:8px 12px;background:#fdf3c4;border-radius:10px;font-weight:800;">
                    {{ $activePack->expires_at->setTimezone('Asia/Kathmandu')->format('Y-m-d H:i') }}
                </span>
                @if (($cooldownRemaining ?? 0) > 0)
                    <span id="cooldown-wrap" style="display:inline-flex;align-items:center;gap:6px;margin-left:8px;">
                        <strong>Next submit:</strong>
                        <span id="cooldown-timer" data-seconds="{{ $cooldownRemaining }}" style="padding:8px 12px;background:#fee2e2;border-radius:10px;font-weight:800;color:#b91c1c;">
                            {{ $cooldownRemaining }}s
                        </span>
                    </span>
                @endif
            @else
                <span style="padding:8px 12px;background:#fee2e2;border-radius:10px;font-weight:800;color:#b91c1c;">No Active Plan</span>
            @endif
        </div>
        @if ($hasActivePack)
            <div style="margin-top:10px;padding:10px 12px;border:1px dashed #fbbf24;background:#fff7ed;border-radius:10px;color:#92400e;font-weight:600;">
                Do not buy a new plan until your existing slots reach zero. If you buy a new plan early, your current slots will be removed and only the new plan’s slots will be added.
            </div>
        @endif
    </div>

    @if ($hasActivePack)
<form action="{{ route('dashboard.submit') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:16px;">
    @csrf
<label for="file" style="font-weight:700;margin-bottom:6px;">Submit a file</label>

    <div style="display:flex; gap:12px; align-items:center;">
        <input 
            type="file" 
            name="file" 
            id="file" 
            required 
            style="width:auto; max-width:300px;"
        >
        <button id="submit-btn" class="btn btn-primary" type="submit">Submit</button>
    </div>
</form>
        
    @endif

    <div style="margin-top:16px;margin-bottom:6px;font-weight:800;font-size:1.4rem;color:#111827;">Your Submissions:</div>
    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;text-align:center;">
            <thead>
                <tr style="text-align:center;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;text-align:center;">Date</th>
                    <th style="padding:10px 6px;text-align:center;">File ID</th>
                    <th style="padding:10px 6px;text-align:center;">Submitted File</th>
                    <th style="padding:10px 6px;text-align:center;">Status</th>
                    <th style="padding:10px 6px;text-align:center;">Similarity Report</th>
                    <th style="padding:10px 6px;text-align:center;">AI Report</th>
                    <th style="padding:10px 6px;text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $submission)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;">{{ $submission->created_at->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;font-weight:700;">{{ $submission->submission_number ?? '—' }}</td>
                        <td style="padding:10px 6px;">
                            <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank">{{ $submission->original_name }}</a>
                        </td>
                        <td style="padding:10px 6px;font-weight:700;{{ $submission->status === 'completed' ? 'color:#1b8d5a;' : 'color:#d97706;' }}">{{ ucfirst($submission->status) }}</td>
                        <td style="padding:10px 6px;">
                            @if ($submission->similarity_report_path)
                                <a href="{{ route('submissions.download.similarity', $submission) }}" target="_blank">Download</a>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:10px 6px;">
                            @if ($submission->ai_report_path)
                                <a href="{{ route('submissions.download.ai', $submission) }}" target="_blank">Download</a>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:10px 6px;">
                            <form id="delete-form-{{ $submission->id }}" action="{{ route('dashboard.submissions.destroy', $submission) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-trigger" data-target="delete-form-{{ $submission->id }}" title="Delete file and reports" style="background:none;border:none;cursor:pointer;padding:0;font-size:1.1rem;">🗑️</button>
                            </form>
                        </td>
                </tr>
            @empty
                    <tr><td colspan="7" style="padding:10px 6px;color:#6b7280;">No submissions yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div id="delete-modal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);">
        <div style="background:#fff;border-radius:12px;padding:18px 20px;max-width:360px;width:90%;box-shadow:0 18px 48px rgba(0,0,0,0.18);text-align:center;">
            <div style="font-weight:800;font-size:1.1rem;color:#111827;margin-bottom:6px;">Delete submission?</div>
            <p style="color:#4b5563;font-size:0.95rem;margin-bottom:14px;">This removes the original file and all associated reports permanently from the server.</p>
            <div style="display:flex;gap:10px;justify-content:center;">
                <button type="button" id="delete-cancel" style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;cursor:pointer;">Cancel</button>
                <button type="button" id="delete-confirm" style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:8px 12px;cursor:pointer;">Delete</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const toggle = document.getElementById('profile-toggle');
            const menu = document.getElementById('profile-menu');
            if (toggle && menu) {
                toggle.addEventListener('click', () => {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                });
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#profile-menu') && !e.target.closest('#profile-toggle')) {
                        menu.style.display = 'none';
                    }
                });
            }

            let pendingForm = null;
            const modal = document.getElementById('delete-modal');
            const btnConfirm = document.getElementById('delete-confirm');
            const btnCancel = document.getElementById('delete-cancel');

            document.querySelectorAll('.delete-trigger').forEach(btn => {
                btn.addEventListener('click', () => {
                    pendingForm = document.getElementById(btn.dataset.target);
                    if (modal) modal.style.display = 'flex';
                });
            });

            btnCancel?.addEventListener('click', () => {
                pendingForm = null;
                if (modal) modal.style.display = 'none';
            });

            btnConfirm?.addEventListener('click', () => {
                if (pendingForm) pendingForm.submit();
                pendingForm = null;
                if (modal) modal.style.display = 'none';
            });

            modal?.addEventListener('click', (e) => {
                if (e.target === modal) {
                    pendingForm = null;
                    modal.style.display = 'none';
                }
            });

            const cooldownEl = document.getElementById('cooldown-timer');
            const cooldownWrap = document.getElementById('cooldown-wrap');
            const submitBtn = document.getElementById('submit-btn');
            if (cooldownEl) {
                let seconds = parseInt(cooldownEl.dataset.seconds || '0', 10);
                if (seconds > 0 && submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                }
                const tick = () => {
                    if (seconds <= 0) {
                        if (cooldownWrap) {
                            cooldownWrap.style.display = 'none';
                        } else {
                            cooldownEl.style.display = 'none';
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '';
                            submitBtn.style.cursor = '';
                        }
                        clearInterval(timer);
                        return;
                    }
                    cooldownEl.textContent = `${seconds}s`;
                    seconds -= 1;
                };
                tick();
                const timer = setInterval(tick, 1000);
            }
        })();
    </script>

    {{ $submissions->links() }}
@endsection
