<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Baccosense')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"
            integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8b+z4+2e5c5e5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous">
        @stack('styles')
    </head>

    <body class="max-w-screen bg-gray-200">
        <x-navbar />

        @yield('content')
        <x-footer />
        @stack('scripts')
    </body>

</html>
