<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    @include('layouts.partials/title-meta', ['title' => $title])
    @include('layouts.partials/head-css')

</head>

<body class="bg-white">

    <div class="maintenance-pages">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="text-center">
                        <div class="mb-5 text-center">
                            <a href="{{ url('/') }}" class="auth-logo">
                                <img src="/images/logo-dark.png" alt="{{ config('app.name') }}" class="mx-auto" height="28" />
                            </a>
                        </div>

                        <div class="maintenance-img">
                            <img src="/images/svg/{{ $image }}" class="img-fluid" alt="">
                        </div>

                        <div class="text-center">
                            <h3 class="mt-5 fw-semibold text-black">{{ $title }}</h3>
                            <p class="text-muted">{{ $message }}</p>
                        </div>

                        <a class="btn btn-primary mt-3 me-1" href="{{ url('/') }}">{{ __('Back to dashboard') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@vite(['resources/js/app.js'])
@include('layouts.partials/vendor')

</body>
</html>
