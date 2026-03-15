<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Pelayanan Desa')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 min-h-screen">
        @include('components.navbar')
        <main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20 py-8">
            @yield('content')
        </main>
    </body>
</html>