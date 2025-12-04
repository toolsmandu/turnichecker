@extends('layouts.panel', ['title' => 'Change Password'])

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Edit Phone</div>
        </div>
        <a href="{{ route('dashboard') }}" class="status" style="text-decoration:none;">Back to Dashboard</a>
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

    <form method="POST" action="{{ route('account.password.update') }}" style="display:grid;gap:12px;max-width:420px;">
        @csrf
        <div>
            <label>Your Email</label>
            <div style="padding:12px 14px;border:1px solid #e5e7eb;border-radius:10px;background:#f9fafc;color:#111827;font-weight:600;">
                {{ auth()->user()->email }}
            </div>
        </div>
        <div>
            <label for="whatsapp">Your WhatsApp Number (with country code)</label>
            <input id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp', auth()->user()->whatsapp) }}" placeholder="+977XXXXXXXXXX">
        </div>
        <div class="actions">
            <button class="btn btn-primary" type="submit">Update Phone</button>
        </div>
                <div>
            <div style="font-weight:800;font-size:1.4rem;">Change Password</div>
        </div>
        <div>
            <label for="current_password">Current Password</label>
            <input id="current_password" type="password" name="current_password">
        </div>
        <div>
            <label for="password">New Password</label>
            <input id="password" type="password" name="password">
        </div>
        <div>
            <label for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
        </div>
        <div class="actions">
            <button class="btn btn-primary" type="submit">Update Password</button>
        </div>
    </form>
@endsection
