@extends('layouts.auth', ['title' => __('Forgot password')])

@section('content')
<p class="text-muted">{{ __('Enter your email address and we will send you a link to reset your password.') }}</p>

<form method="POST" action="{{ route('password.email') }}" class="my-4">
    @csrf

    <div class="form-group mb-3">
        <label for="email" class="form-label">{{ __('Email address') }}</label>
        <input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="d-grid">
        <button class="btn btn-primary" type="submit">{{ __('Send reset link') }}</button>
    </div>
</form>

<div class="text-center text-muted">
    <a class="text-primary fw-medium" href="{{ route('login') }}">{{ __('Back to log in') }}</a>
</div>
@endsection
