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

    <main class="max-w-[1440px] mx-auto px-4 pt-[107px] pb-6 sm:px-6 lg:px-[40px] lg:pt-[117px] lg:pb-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
