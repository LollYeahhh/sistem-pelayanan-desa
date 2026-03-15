<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Pelayanan Desa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-[#F1F5F9] min-h-screen font-sans">

    @include('components.navbar-admin')

    <main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-[40px] py-6 lg:py-8">
        @yield('content')
    </main>

</body>
</html>