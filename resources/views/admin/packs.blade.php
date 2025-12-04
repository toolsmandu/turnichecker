@extends('layouts.panel', ['title' => 'Packs'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Subscription Management</div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Back to Dashboard</a>
            <button class="btn btn-ghost" type="submit">Logout</button>
        </form>
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

    <h3 style="margin:12px 0 6px;">Create Customer</h3>
    <form action="{{ route('admin.packs.customers.create') }}" method="POST" style="display:grid;gap:12px;margin-bottom:18px;">
        @csrf
        <div class="row two">
            <div>
                <label>Email</label>
                <input name="email" type="email" required placeholder="customer@example.com">
            </div>
            <div>
                <label>WhatsApp (with country code)</label>
                <input name="whatsapp" type="text" required placeholder="+977XXXXXXXXXX">
            </div>
            <div>
                <label>Assign Pack (optional)</label>
                <select name="pack_id" style="width:100%;padding:10px;border:1px solid #dfe3eb;border-radius:10px;background:#f9fafc;">
                    <option value="">Skip</option>
                    @foreach ($packs as $pack)
                        <option value="{{ $pack->id }}">{{ $pack->name }} ({{ $pack->quota }} / {{ $pack->duration_days }} days)</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="actions"><button class="btn btn-primary" type="submit">Create Customer</button></div>
    </form>

    <h3 style="margin:12px 0 6px;">Assign Pack to Customer</h3>
    <form action="{{ route('admin.packs.assign') }}" method="POST" style="display:grid;gap:12px;margin-bottom:18px;">
        @csrf
        <div class="row two">
            <div style="position:relative;">
                <label>Customer</label>
                <input type="hidden" name="user_id" id="customer-hidden" required>
                <div id="customer-select" style="border:1px solid #dfe3eb;border-radius:10px;background:#f9fafc;padding:0;position:relative;">
                    <input type="text" id="customer-search" placeholder="Search customer email..." style="width:100%;padding:10px;border:0;border-bottom:1px solid #e5e7eb;background:#fff;border-top-left-radius:10px;border-top-right-radius:10px;">
                    <div id="customer-options" style="max-height:180px;overflow:auto;">
                        <div data-id="" style="padding:10px;cursor:pointer;color:#6b7280;">Select customer</div>
                        @foreach ($users as $user)
                            <div data-id="{{ $user->id }}" style="padding:10px;cursor:pointer;">{{ $user->email }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <label>Pack</label>
                <select name="pack_id" required style="width:100%;padding:10px;border:1px solid #dfe3eb;border-radius:10px;background:#f9fafc;">
                    <option value="">Select pack</option>
                    @foreach ($packs as $pack)
                        <option value="{{ $pack->id }}">{{ $pack->name }} ({{ $pack->quota }} uploads / {{ $pack->duration_days }} days)</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="actions"><button class="btn btn-primary">Assign Pack</button></div>
    </form>

    <script>
        const customerSearch = document.getElementById('customer-search');
        const customerOptions = document.getElementById('customer-options');
        const hiddenCustomer = document.getElementById('customer-hidden');

        function filterCustomers(term) {
            const rows = Array.from(customerOptions.querySelectorAll('[data-id]'));
            rows.forEach(row => {
                const match = row.textContent.toLowerCase().includes(term.toLowerCase());
                row.style.display = match ? 'block' : 'none';
            });
        }

        customerOptions?.addEventListener('click', (e) => {
            const target = e.target.closest('[data-id]');
            if (!target) return;
            hiddenCustomer.value = target.dataset.id;
            customerSearch.value = target.textContent.trim();
        });

        customerSearch?.addEventListener('input', (e) => {
            hiddenCustomer.value = ''; // clear selection when typing
            filterCustomers(e.target.value);
        });
    </script>

    <h3 style="margin:12px 0 6px;">Existing Packs</h3>
    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 6px;">Name</th>
                    <th style="padding:10px 6px;">Quota</th>
                    <th style="padding:10px 6px;">Duration</th>
                    <th style="padding:10px 6px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packs as $pack)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px 6px;">
                            <input form="pack-{{ $pack->id }}" name="name" value="{{ $pack->name }}" style="width:140px;padding:8px;border:1px solid #dfe3eb;border-radius:8px;">
                        </td>
                        <td style="padding:10px 6px;">
                            <input form="pack-{{ $pack->id }}" type="number" name="quota" value="{{ $pack->quota }}" min="1" style="width:90px;padding:8px;border:1px solid #dfe3eb;border-radius:8px;">
                        </td>
                        <td style="padding:10px 6px;">
                            <input form="pack-{{ $pack->id }}" type="number" name="duration_days" value="{{ $pack->duration_days }}" min="1" style="width:100px;padding:8px;border:1px solid #dfe3eb;border-radius:8px;">
                        </td>
                        <td style="padding:10px 6px;">
                            <form id="pack-{{ $pack->id }}" action="{{ route('admin.packs.update', $pack) }}" method="POST" style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-primary" type="submit" style="padding:8px 12px;">Update</button>
                                <button form="delete-{{ $pack->id }}" class="btn btn-ghost" type="submit" style="padding:8px 10px;background:#ffecec;">Delete</button>
                            </form>
                            <form id="delete-{{ $pack->id }}" action="{{ route('admin.packs.destroy', $pack) }}" method="POST">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:10px 6px;color:#6b7280;">No packs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 style="margin:12px 0 6px;">Create Pack</h3>
    <form action="{{ route('admin.packs.store') }}" method="POST" style="display:grid;gap:12px;margin-bottom:18px;">
        @csrf
        <div class="row two">
            <div>
                <label>Name</label>
                <input name="name" required>
            </div>
            <div>
                <label>Quota</label>
                <input type="number" name="quota" min="1" required>
            </div>
        </div>
        <div class="row two">
            <div>
                <label>Duration (days)</label>
                <input type="number" name="duration_days" value="7" min="7" max="7" readonly style="background:#f5f5f5;">
            </div>
        </div>
        <div class="actions"><button class="btn btn-primary">Save Pack</button></div>
    </form>
@endsection
