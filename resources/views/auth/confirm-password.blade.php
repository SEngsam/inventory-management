@extends('layouts.auth', ['title' => __('Confirm password')])

@section('content')
<p class="text-muted">{{ __('This is a secure area. Please confirm your password before continuing.') }}</p>

<form method="POST" action="{{ route('password.confirm') }}" class="my-4">
    @csrf

    <div class="form-group mb-3">
        <label for="password" class="form-label">{{ __('Password') }}</label>
        <input class="form-control" type="password" id="password" name="password" required autofocus autocomplete="current-password">
    </div>

    <div class="d-grid">
        <button class="btn btn-primary" type="submit">{{ __('Confirm') }}</button>
    </div>
</form>
@endsection
