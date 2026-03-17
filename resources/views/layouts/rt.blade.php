<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield ("title", "Portal RT - Pelayanan Desa")</title>
    @vite (["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="min-h-screen bg-[#F5F9FF] font-sans">
    @include ("components.navbar-rt")

    <main class="mx-auto max-w-[1440px] px-4 pb-6 pt-[115px] sm:px-6 lg:px-[40px] lg:pb-8 lg:pt-[137px]">
        @if (session("success"))
            <div class="mb-6 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 animate-fade-in">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session("success") }}
            </div>
        @endif

        @if (session("error"))
            <div class="mb-6 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 animate-fade-in">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {{ session("error") }}
            </div>
        @endif

        @yield ("content")
    </main>
</body>
</html>
