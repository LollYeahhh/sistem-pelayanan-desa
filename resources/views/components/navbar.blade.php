{{-- Navbar Setelah Login - sesuai desain Figma --}}
<nav class="bg-white shadow-sm border-b">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20">
        <div class="flex justify-between items-center h-[91px]">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-tegal.png') }}" alt="Logo" class="h-[50px] w-[45px] object-contain">
                <div>
                    <h1 class="text-lg font-extrabold text-black leading-tight">Desa Dukuhtengah</h1>
                    <p class="text-[10px] font-medium text-gray-400 tracking-[1px] uppercase">Kecamatan Bojong</p>
                </div>
            </a>

            {{-- Menu berdasarkan Role --}}
            <div class="hidden lg:flex items-center gap-8">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-base font-medium text-gray-800 hover:text-primary transition">Dashboard</a>
                    {{-- Menu admin lainnya akan ditambahkan nanti --}}
                @elseif(auth()->user()->isRt())
                    <a href="{{ route('rt.dashboard') }}" class="text-base font-medium text-gray-800 hover:text-primary transition">Dashboard</a>
                    {{-- Menu RT lainnya akan ditambahkan nanti --}}
                @else
                    <a href="{{ route('warga.dashboard') }}" class="text-base font-medium text-gray-800 hover:text-primary transition">Dashboard</a>
                    {{-- Menu warga lainnya akan ditambahkan nanti --}}
                @endif
            </div>

            {{-- User Info + Logout --}}
            <div class="flex items-center gap-4">
                <span class="hidden sm:block text-sm text-gray-600 font-medium">
                    {{ auth()->user()->nama_lengkap }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:underline font-medium">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>