@extends('layouts.panel', ['title' => 'Login'])

@section('content')
    <div style="text-align:center;margin-bottom:14px;">
        <div style="font-size:1.5rem;font-weight:800;">Welcome back</div>
        <p style="color:#5d6b80;margin-top:6px;">Sign in to manage content or continue checking documents.</p>
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

    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="row">
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                <label style="display:flex;align-items:center;gap:8px;font-weight:500;color:#5d6b80;">
                <div style="display:flex;gap:12px;align-items:center;">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                    <a href="{{ url('/register') }}">Create account</a>
                </div>
            </div>
        </div>
        <div class="actions" style="margin-top:12px;">
            <button class="btn btn-primary" type="submit">Login</button>
        </div>
    </form>
@endsection
