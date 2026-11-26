@extends('layouts.auth', ['title' => __('Register')])

@section('content')
<form method="POST" action="{{ route('register') }}" class="my-4">
    @csrf

    <div class="form-group mb-3">
        <label for="name" class="form-label">{{ __('Name') }}</label>
        <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
    </div>

    <div class="form-group mb-3">
        <label for="email" class="form-label">{{ __('Email address') }}</label>
        <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label">{{ __('Password') }}</label>
        <input class="form-control" type="password" id="password" name="password" required autocomplete="new-password">
    </div>

    <div class="form-group mb-3">
        <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
    </div>

    <div class="d-grid">
        <button class="btn btn-primary" type="submit">{{ __('Register') }}</button>
    </div>
</form>

<div class="text-center text-muted">
    <p class="mb-0">{{ __('Already have an account?') }}
        <a class="text-primary ms-2 fw-medium" href="{{ route('login') }}">{{ __('Log in') }}</a>
    </p>
</div>
@endsection
