@extends('layouts.panel', ['title' => 'Expired Packs'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Expired Packs</div>
            <p style="color:#5d6b80;margin-top:6px;">Packs that have passed their expiry date.</p>
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
                    <th style="padding:10px 6px;">Customer Email</th>
                    <th style="padding:10px 6px;">Phone</th>
                    <th style="padding:10px 6px;">Pack</th>
                    <th style="padding:10px 6px;">Purchased At</th>
                    <th style="padding:10px 6px;">Expired At</th>
                    <th style="padding:10px 6px;">Hide</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expiredPacks as $pack)
                    <tr style="border-bottom:1px solid #e5e7eb;" data-expired-id="{{ $pack->id }}">
                        <td style="padding:10px 6px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;font-weight:700;">{{ $pack->order_id }}</td>
                        <td style="padding:10px 6px;">{{ $pack->user->email ?? '—' }}</td>
                        <td style="padding:10px 6px;">{{ $pack->user->whatsapp ?? '—' }}</td>
                        <td style="padding:10px 6px;">{{ $pack->pack->name ?? 'Pack' }}</td>
                        <td style="padding:10px 6px;">{{ $pack->created_at?->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;">{{ $pack->expires_at?->timezone('Asia/Kathmandu')->format('Y-m-d H:i') }}</td>
                        <td style="padding:10px 6px;text-align:center;">
                            <input type="checkbox" class="expired-dismiss" data-id="{{ $pack->id }}">
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:10px 6px;color:#6b7280;">No expired packs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">
        {{ $expiredPacks->links() }}
    </div>

    <script>
        (function() {
            const storageKey = 'dismissedExpiredIds';
            const table = document.querySelector('[data-expired-id]')?.closest('tbody');
            if (!table) return;

            const dismissed = new Set(JSON.parse(localStorage.getItem(storageKey) || '[]'));

            const rows = Array.from(table.querySelectorAll('tr[data-expired-id]'));
            rows.forEach(row => {
                const id = row.getAttribute('data-expired-id');
                if (dismissed.has(id)) {
                    row.remove();
                    return;
                }
                const checkbox = row.querySelector('.expired-dismiss');
                if (checkbox) {
                    checkbox.checked = dismissed.has(id);
                    checkbox.addEventListener('change', () => {
                        if (checkbox.checked) {
                            dismissed.add(id);
                            row.remove();
                        } else {
                            dismissed.delete(id);
                        }
                        localStorage.setItem(storageKey, JSON.stringify(Array.from(dismissed)));
                    });
                }
            });
        })();
    </script>
@endsection
