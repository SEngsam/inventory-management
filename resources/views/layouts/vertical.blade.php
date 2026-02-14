<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials/title-meta', ['title' => $title??"Invenotry App"])
    @yield('css')
    @include('layouts.partials/head-css')
    @livewireStyles

</head>

<body data-menu-color="light" data-sidebar="default" @yield('body') >

<div id="app-layout">

    @include('layouts.partials/topbar')
    @include('layouts.partials/sidebar')

    <div class="content-page">
        <div class="content">
            <div class="container-xxl">
               {{ $slot}}
                {{-- @yield('content') --}}
            </div>
        </div>

        @include("layouts.partials/footer")

    </div>

</div>

@vite(['resources/js/app.js'])
@include("layouts.partials/vendor")
@livewireScripts


</body>
</html>
