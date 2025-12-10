@extends('layouts.panel', ['title' => 'Sales History'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Sales History</div>
            <p style="color:#5d6b80;margin-top:6px;">Recent pack purchases.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Back to Dashboard</a>
            <button class="btn btn-ghost" type="submit">Logout</button>
        </form>
    </div>

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;">Order ID</th>
                    <th style="padding:10px 6px;">Purchase Date</th>
                    <th style="padding:10px 6px;">Customer Email</th>
                    <th style="padding:10px 6px;">Phone</th>
                    <th style="padding:10px 6px;">Pack</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($salesHistory as $sale)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;font-weight:700;">{{ $sale->order_id }}</td>
                        <td style="padding:10px 6px;">{{ $sale->created_at?->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;">{{ $sale->user->email ?? '—' }}</td>
                        <td style="padding:10px 6px;">{{ $sale->user->whatsapp ?? '—' }}</td>
                        <td style="padding:10px 6px;">{{ $sale->pack->name ?? 'Pack' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:10px 6px;color:#6b7280;">No sales yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">
        {{ $salesHistory->links() }}
    </div>
@endsection
