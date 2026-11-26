@extends('layouts.auth', ['title' => __('Verify email')])

@section('content')
<p class="text-muted">{{ __('Please verify your email address by clicking the link we just emailed to you.') }}</p>

@if (session('status') === 'verification-link-sent')
    <div class="alert alert-success">{{ __('A new verification link has been sent to your email address.') }}</div>
@endif

<form method="POST" action="{{ route('verification.send') }}" class="my-4">
    @csrf
    <div class="d-grid">
        <button class="btn btn-primary" type="submit">{{ __('Resend verification email') }}</button>
    </div>
</form>

<form method="POST" action="{{ route('logout') }}" class="text-center">
    @csrf
    <button type="submit" class="btn btn-link text-muted">{{ __('Log out') }}</button>
</form>
@endsection
