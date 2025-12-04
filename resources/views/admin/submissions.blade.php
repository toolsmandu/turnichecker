@extends('layouts.panel', ['title' => 'Submissions'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Submissions</div>
            <p style="color:#5d6b80;margin-top:6px;">Upload reports, mark complete, or refund quota.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-ghost" type="submit">Logout</button>
        </form>
    </div>

    @if (session('status'))
        <div class="status" style="background:#e9f9f1;border-color:#c7f1dc;color:#1b8d5a;margin-bottom:12px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;">Date</th>
                    <th style="padding:10px 6px;">Customer</th>
                    <th style="padding:10px 6px;">Status</th>
                    <th style="padding:10px 6px;">Submitted File</th>
                    <th style="padding:10px 6px;">Similarity</th>
                    <th style="padding:10px 6px;">AI</th>
                    <th style="padding:10px 6px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submissions as $submission)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;">{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;">
                            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                                <span>{{ $submission->user->email }}</span>
                                <form action="{{ route('admin.impersonate.start', $submission->user) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-ghost" type="submit" style="padding:6px 10px;">Login as</button>
                                </form>
                            </div>
                        </td>
                        <td style="padding:10px 6px;font-weight:700;{{ $submission->status === 'completed' ? 'color:#1b8d5a;' : 'color:#d97706;' }}">{{ ucfirst($submission->status) }}</td>
                        <td style="padding:10px 6px;">
                            <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank">{{ $submission->original_name }}</a>
                        </td>
                        <td style="padding:10px 6px;">
                            @if ($submission->similarity_report_path)
                                <a href="{{ asset('storage/'.$submission->similarity_report_path) }}" target="_blank">Existing</a>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:10px 6px;">
                            @if ($submission->ai_report_path)
                                <a href="{{ asset('storage/'.$submission->ai_report_path) }}" target="_blank">Existing</a>
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:10px 6px;">
                            <form action="{{ route('admin.submissions.update', $submission) }}" method="POST" enctype="multipart/form-data" style="display:grid;gap:8px;">
                                @csrf
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <input type="file" name="similarity_report" style="font-size:0.95rem;">
                                    <input type="file" name="ai_report" style="font-size:0.95rem;">
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                    <label style="display:flex;align-items:center;gap:6px;font-weight:600;color:#0f8bff;">
                                        <input type="checkbox" name="refund" value="1"> Refund
                                    </label>
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <button class="btn btn-primary" name="status" value="completed" type="submit">Update</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $submissions->links() }}
@endsection
