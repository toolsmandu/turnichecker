@extends('layouts.panel', ['title' => 'Reset Password'])

@section('content')
    <div style="text-align:center;margin-bottom:14px;">
        <div style="font-size:1.5rem;font-weight:800;">Create a new password</div>
        <p style="color:#5d6b80;margin-top:6px;">Choose a strong password to secure your account.</p>
    </div>

    @if (session('status'))
        <div class="status" style="margin-bottom:12px;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="status" style="background:#fff4f3;border-color:#f2c8c3;color:#b91c1c;margin-bottom:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="row">
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            </div>
            <div class="row two">
                <div>
                    <label for="password">New Password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <div>
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
            </div>
            <div style="text-align:right;margin-top:6px;">
                <a href="{{ route('login') }}">Back to login</a>
            </div>
        </div>
        <div class="actions" style="margin-top:12px;">
            <button class="btn btn-primary" type="submit">Update password</button>
        </div>
    </form>
@endsection
