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
    @if (session('new_customer'))
        @php
            $newCustomer = session('new_customer');
            $newCustomerCopy = "Email:{$newCustomer['email']}\nPassword:{$newCustomer['password']}\nLogin URL:{$newCustomer['login_url']}\n\nNote: Login and Submit your files. You will get your reports within 1 hour.";
        @endphp
        <div id="customer-popup" data-copy="{{ $newCustomerCopy }}" style="position:fixed;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;z-index:1000;">
            <div style="background:#fff;border-radius:14px;box-shadow:0 12px 40px rgba(0,0,0,0.14);max-width:520px;width:90%;padding:18px;position:relative;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px;">
                    <div style="font-weight:700;font-size:1.05rem;">Customer Created</div>
                    <button type="button" data-close-customer-popup style="border:0;background:transparent;font-size:1.2rem;cursor:pointer;line-height:1;color:#6b7280;">×</button>
                </div>
                <div style="display:grid;gap:8px;margin-bottom:12px;font-size:0.97rem;">
                    <div><strong>Email:</strong> {{ $newCustomer['email'] }}</div>
                    <div><strong>Password:</strong> {{ $newCustomer['password'] }}</div>
                    <div><strong>Login URL:</strong> <a href="{{ $newCustomer['login_url'] }}" target="_blank" style="color:#2563eb;text-decoration:none;">{{ $newCustomer['login_url'] }}</a></div>
                </div>
                <div style="background:#f8fafc;border:1px dashed #d1d5db;border-radius:10px;padding:10px 12px;white-space:pre-line;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;font-size:0.92rem;margin-bottom:14px;">
                    Email:{{ $newCustomer['email'] }}
Password:{{ $newCustomer['password'] }}
Login URL:{{ $newCustomer['login_url'] }}

Note: Login and Submit your files. You will get your reports within 1 hour.
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <button id="customer-popup-copy" class="btn btn-primary" type="button">Copy details</button>
                    <button data-close-customer-popup class="btn btn-ghost" type="button">Close</button>
                    <span id="customer-popup-copy-status" style="color:#16a34a;font-weight:600;font-size:0.95rem;display:none;">Copied!</span>
                </div>
            </div>
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

        const customerPopup = document.getElementById('customer-popup');
        if (customerPopup) {
            const copyButton = document.getElementById('customer-popup-copy');
            const copyStatus = document.getElementById('customer-popup-copy-status');
            const closeButtons = customerPopup.querySelectorAll('[data-close-customer-popup]');
            const closePopup = () => {
                customerPopup.style.display = 'none';
            };

            closeButtons.forEach(btn => btn.addEventListener('click', closePopup));

            copyButton?.addEventListener('click', async () => {
                const textToCopy = customerPopup.dataset.copy || '';

                try {
                    if (navigator?.clipboard?.writeText) {
                        await navigator.clipboard.writeText(textToCopy);
                    } else {
                        const temp = document.createElement('textarea');
                        temp.value = textToCopy;
                        temp.setAttribute('readonly', '');
                        temp.style.position = 'absolute';
                        temp.style.left = '-9999px';
                        document.body.appendChild(temp);
                        temp.select();
                        document.execCommand('copy');
                        document.body.removeChild(temp);
                    }
                    if (copyStatus) {
                        copyStatus.style.display = 'inline';
                        setTimeout(() => (copyStatus.style.display = 'none'), 1800);
                    }
                } catch (e) {
                    alert('Could not copy to clipboard. Please copy manually.');
                }
            });
        }
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
