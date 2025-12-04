@extends('layouts.panel', ['title' => 'Register'])

@section('content')
    <div style="text-align:center;margin-bottom:14px;">
        <div style="font-size:1.5rem;font-weight:800;">Create your account</div>
    </div>

    @if ($errors->any())
        <div class="status" style="background:#fff4f3;border-color:#f2c8c3;color:#b91c1c;margin-bottom:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <div class="row">
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div>
                <label for="whatsapp">WhatsApp (with country code)</label>
                <input id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="+977XXXXXXXXXX">
            </div>
            <div class="row two">
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <div>
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
            </div>
            <div style="text-align:right;margin-top:6px;">
                <a href="{{ url('/login') }}">Already registered?</a>
            </div>
        </div>
        <div class="actions" style="margin-top:12px;">
            <button class="btn btn-primary" type="submit">Sign Up</button>
        </div>
    </form>
@endsection
