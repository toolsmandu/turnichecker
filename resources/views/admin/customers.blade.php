@extends('layouts.panel', ['title' => 'Customers'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Customer List</div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Back to Dashboard</a>
            <button class="btn btn-ghost" type="submit">Logout</button>
        </form>
    </div>

    @if (session('status'))
        <div class="status" style="background:#e9f9f1;border-color:#c7f1dc;color:#1b8d5a;margin-bottom:12px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;text-align:left;">
            <thead>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;">Email</th>
                    <th style="padding:10px 6px;">Phone</th>
                    <th style="padding:10px 6px;">Subscription</th>
                    <th style="padding:10px 6px;">Quota</th>
                    <th style="padding:10px 6px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    @php
                        $activePack = $customer->packs->first(function ($pack) {
                            return $pack->expires_at && $pack->expires_at->isFuture() && $pack->quota_remaining > 0;
                        });
                        $latestPack = $customer->packs->first();
                    @endphp
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;">{{ $customer->email }}</td>
                        <td style="padding:10px 6px;">{{ $customer->whatsapp ?? '—' }}</td>
                        <td style="padding:10px 6px;font-weight:700;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="color:{{ $customer->subscription_active ? '#1b8d5a' : '#b91c1c' }};">
                                    {{ $customer->subscription_active ? 'Active' : 'Inactive' }}
                                </span>
                                <form action="{{ route('admin.customers.subscription.update', $customer) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="subscription_active" value="{{ $customer->subscription_active ? 0 : 1 }}">
                                    <button class="btn btn-ghost" type="submit" style="padding:6px 10px; background: {{ $customer->subscription_active ? '#f3f4f6' : '#16a34a' }}; color: {{ $customer->subscription_active ? 'inherit' : '#fff' }};">
                                        {{ $customer->subscription_active ? 'Mark Inactive' : 'Mark Active' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td style="padding:10px 6px;">
                            <form action="{{ route('admin.customers.quota.update', $customer) }}" method="POST" style="display:flex;gap:6px;align-items:center;">
                                @csrf
                                <input type="number" name="quota_remaining" value="{{ old('quota_remaining', $latestPack?->quota_remaining ?? 0) }}" min="0" style="width:80px;padding:8px;border:1px solid #dfe3eb;border-radius:8px;">
                                <button class="btn btn-ghost" type="submit" style="padding:8px 10px;">Update</button>
                            </form>
                        </td>
                        <td style="padding:10px 6px;">
                            <form action="{{ route('admin.impersonate.start', $customer) }}" method="POST">
                                @csrf
                                <button class="btn btn-ghost" type="submit" style="padding:6px 10px; background:#38bdf8; color:#0b2e46;">Login as</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:10px 6px;color:#6b7280;">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $customers->links() }}
@endsection
