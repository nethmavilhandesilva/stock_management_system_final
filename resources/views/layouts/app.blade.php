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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



    @stack('styles')
</head>
<body>

@include('layouts.partials.header')

<div class="main-wrapper container-fluid"> {{-- Use container-fluid for full width or container for fixed width --}}
    <div class="row"> {{-- Start a Bootstrap row --}}
        <div class="col-md-3"> {{-- This column will hold your sidebar and take up 3 units out of 12 --}}
            @include('layouts.partials.sidebar')
        </div>

        <div class="col-md-9"> {{-- This column will hold your main content and take up 9 units out of 12 --}}
            @include('layouts.partials.navbar')
            <main class="main users chart-page" id="skip-target">
                @yield('content')
            </main>
        </div>
    </div> {{-- End the Bootstrap row --}}

    @include('layouts.partials.footer')
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>