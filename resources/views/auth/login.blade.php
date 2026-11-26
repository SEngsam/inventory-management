@extends('layouts.auth', ['title' => __('Log in')])

@section('content')
<form method="POST" action="{{ route('login') }}" class="my-4">
    @csrf

    <div class="form-group mb-3">
        <label for="email" class="form-label">{{ __('Email address') }}</label>
        <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label">{{ __('Password') }}</label>
        <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
    </div>

    <div class="form-group d-flex mb-3">
        <div class="col-sm-6">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">{{ __('Remember me') }}</label>
            </div>
        </div>
        <div class="col-sm-6 text-end">
            <a class="text-muted fs-14" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
        </div>
    </div>

    <div class="d-grid">
        <button class="btn btn-primary" type="submit">{{ __('Log in') }}</button>
    </div>
</form>

<div class="text-center text-muted">
    <p class="mb-0">{{ __("Don't have an account?") }}
        <a class="text-primary ms-2 fw-medium" href="{{ route('register') }}">{{ __('Sign up') }}</a>
    </p>
</div>
@endsection
