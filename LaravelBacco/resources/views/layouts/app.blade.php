<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Baccosense')</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif
        <link type="image/x-icon" href="/favicon.ico" rel="shortcut icon">
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"
            crossorigin="anonymous">
        @stack('styles')

        <title>Data Inventaris</title>
        <!-- Font Awesome for Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    </head>

    <body class="max-w-screen bg-gray-200">
        <x-navbar />

        @yield('content')
        @if (!Route::is('login'))
            <x-footer />
        @endif

        @stack('scripts')
    </body>

</html>
