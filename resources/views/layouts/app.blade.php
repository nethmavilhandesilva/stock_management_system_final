<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manning')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')
</head>
<body>

@include('layouts.partials.header')

{{-- New section for horizontal sidebar --}}
@hasSection('horizontal_sidebar')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @yield('horizontal_sidebar')
            </div>
        </div>
    </div>
@endif

<div class="main-wrapper container-fluid">
    <div class="row">
        {{-- Conditionally include the vertical sidebar --}}
        @if (!trim($__env->yieldContent('horizontal_sidebar')))
            <div class="col-md-3">
                @include('layouts.partials.sidebar')
            </div>
        @endif

        {{-- Adjust column size based on sidebar presence --}}
        <div class="{{ trim($__env->yieldContent('horizontal_sidebar')) ? 'col-md-12' : 'col-md-9' }}">
            @include('layouts.partials.navbar')
            <main class="main users chart-page" id="skip-target">
                @yield('content')
            </main>
        </div>
    </div>

    @include('layouts.partials.footer')
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>