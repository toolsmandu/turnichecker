@extends('layouts.panel', ['title' => 'Purchase History'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Purchase History</div>
        </div>
        <a href="{{ route('dashboard') }}" class="status" style="text-decoration:none;">Back to Dashboard</a>
    </div>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;">Order ID</th>
                    <th style="padding:10px 6px;">Purchase Date</th>
                    <th style="padding:10px 6px;">Plan</th>
                    <th style="padding:10px 6px;">Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($userPacks as $userPack)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">{{ $userPack->order_id }}</td>
                        <td style="padding:10px 6px;">{{ $userPack->created_at?->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;">{{ $userPack->pack->name ?? 'Pack' }}</td>
                        <td style="padding:10px 6px;">{{ $userPack->expires_at?->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="padding:10px 6px;color:#6b7280;">No purchases yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
