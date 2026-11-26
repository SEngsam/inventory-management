@extends('layouts.auth', ['title' => __('Reset password')])

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="my-4">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group mb-3">
        <label for="email" class="form-label">{{ __('Email address') }}</label>
        <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label">{{ __('New password') }}</label>
        <input class="form-control" type="password" id="password" name="password" required autocomplete="new-password">
    </div>

    <div class="form-group mb-3">
        <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
    </div>

    <div class="d-grid">
        <button class="btn btn-primary" type="submit">{{ __('Reset password') }}</button>
    </div>
</form>
@endsection
