{{-- Navbar Admin --}}
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-[40px]">
        <div class="flex justify-between items-center h-[75px] lg:h-[91px]">

            {{-- Left: Hamburger + Logo --}}
            <div class="flex items-center gap-4 lg:gap-6">
                {{-- Hamburger (mobile sidebar trigger) --}}
                <button id="sidebar-toggle" class="text-gray-600 hover:text-dark" onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                {{-- Logo --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-tegal.png') }}" alt="Logo" class="h-[50px] w-[45px] object-contain">
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-extrabold text-black leading-tight">Desa<br>Dukuhtengah</h1>
                        <p class="text-[9px] font-medium text-gray-400 tracking-[1px] uppercase">Kecamatan Bojong</p>
                    </div>
                </a>
            </div>

            {{-- Center: Search --}}
            <div class="hidden md:flex items-center flex-1 max-w-[400px] mx-8">
                <div class="relative w-full">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" placeholder="Cari Data"
                        class="w-full bg-[#F1F5F9] border-0 rounded-full h-[44px] pl-12 pr-4 text-sm text-gray-600 placeholder-gray-400 focus:ring-2 focus:ring-primary/30">
                </div>
            </div>

            {{-- Right: Notification + Profile --}}
            <div class="flex items-center gap-4 lg:gap-6">
                {{-- Notification --}}
                <button class="relative text-gray-600 hover:text-dark">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                {{-- Profile --}}
                <div class="relative">
                    <button onclick="document.getElementById('profile-dropdown').classList.toggle('hidden')" class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                    </button>

                    {{-- Dropdown --}}
                    <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                        <p class="px-4 py-2 text-sm font-bold text-dark">{{ auth()->user()->nama_lengkap }}</p>
                        <p class="px-4 pb-2 text-xs text-gray border-b border-gray-100">{{ ucfirst(auth()->user()->role) }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Mobile Sidebar Overlay --}}
<div id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full transition-transform duration-300">
    <div class="p-6">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg font-bold text-dark">Menu Admin</h2>
            <button onclick="document.getElementById('admin-sidebar').classList.add('-translate-x-full')" class="text-gray-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-primary/10 text-primary">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" /></svg>
                Dashboard
            </a>
        </nav>
    </div>
</div>