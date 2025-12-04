@extends('layouts.panel', ['title' => 'Forgot Password'])

@section('content')
    <div style="text-align:center;margin-bottom:14px;">
        <div style="font-size:1.5rem;font-weight:800;">Reset your password</div>
        <p style="color:#5d6b80;margin-top:6px;">Enter your email and we will send you a link to create a new password.</p>
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="row">
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div style="text-align:right;margin-top:6px;">
                <a href="{{ route('login') }}">Back to login</a>
            </div>
        </div>
        <div class="actions" style="margin-top:12px;">
            <button class="btn btn-primary" type="submit">Send reset link</button>
        </div>
    </form>
@endsection
