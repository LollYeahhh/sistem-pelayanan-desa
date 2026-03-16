{{-- Navbar Publik - sesuai desain Figma Desktop-73 --}}
<nav
    class="sticky top-0 z-50 bg-[#D8E7FF]/70 backdrop-blur-sm border-b border-[#c5d9f5]/40"
>
    <div class="max-w-[1440px] mx-auto px-6 lg:px-[77px]">
        <div class="flex justify-between items-center h-[91px]">
            {{-- Logo + Nama Desa --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img
                    src="{{ asset('images/logo-tegal.png') }}"
                    alt="Logo"
                    class="h-[60px] w-[54px] object-contain"
                />
                <div class="flex flex-col gap-1">
                    <h1
                        class="text-[18px] font-extrabold text-black leading-[20px]"
                    >
                        Desa<br />Dukuhtengah
                    </h1>
                    <p class="text-[10px] font-medium text-gray-400 tracking-[1px] uppercase leading-[15px]">Kecamatan Bojong</p>
                </div>
            </a>

            {{-- Menu Desktop --}}
            <div class="hidden lg:flex items-center gap-10">
                <a
                    href="#beranda"
                    class="text-[14px] font-medium text-[#282828] hover:text-primary transition"
                    >Beranda</a
                >
                <a
                    href="#layanan"
                    class="text-[14px] font-medium text-[#282828] hover:text-primary transition"
                    >Layanan</a
                >
                <a
                    href="#panduan"
                    class="text-[14px] font-medium text-[#282828] hover:text-primary transition"
                    >Panduan</a
                >
                <a
                    href="#aduan"
                    class="text-[14px] font-medium text-[#282828] hover:text-primary transition"
                    >Aduan Masyarakat</a
                >
            </div>

            {{-- Tombol Masuk --}}
            <div class="hidden lg:block">
                @auth
                    @php
                        $dashRoute = match (auth()->user()->role) {
                            "admin" => "admin.dashboard",
                            "rt" => "rt.dashboard",
                            "warga" => "warga.dashboard",
                        };
                    @endphp
                    <a
                        href="{{ route($dashRoute) }}"
                        class="bg-[#3b82f6] text-white font-bold text-[16px] px-[27px] py-[16px] rounded-[12px] hover:bg-blue-600 transition"
                    >
                        Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="bg-[#3b82f6] text-white font-bold text-[16px] px-[27px] py-[16px] rounded-[12px] hover:bg-blue-600 transition"
                    >
                        Masuk Layanan
                    </a>
                @endauth
            </div>

            {{-- Hamburger Mobile --}}
            <button
                class="lg:hidden flex flex-col gap-1.5 p-2"
                onclick="
                    document
                        .getElementById('mobile-menu')
                        .classList.toggle('hidden')
                "
            >
                <span class="w-6 h-0.5 bg-gray-800"></span>
                <span class="w-6 h-0.5 bg-gray-800"></span>
                <span class="w-6 h-0.5 bg-gray-800"></span>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div
            id="mobile-menu"
            class="hidden lg:hidden pb-6 border-t border-gray-200 mt-2"
        >
            <div class="flex flex-col gap-4 pt-4">
                <a href="#beranda" class="text-base font-medium text-[#282828]"
                    >Beranda</a
                >
                <a href="#layanan" class="text-base font-medium text-[#282828]"
                    >Layanan</a
                >
                <a href="#panduan" class="text-base font-medium text-[#282828]"
                    >Panduan</a
                >
                <a href="#aduan" class="text-base font-medium text-[#282828]"
                    >Aduan Masyarakat</a
                >
                @auth
                    @php
                        $dashRoute = match (auth()->user()->role) {
                            "admin" => "admin.dashboard",
                            "rt" => "rt.dashboard",
                            "warga" => "warga.dashboard",
                        };
                    @endphp
                    <a
                        href="{{ route($dashRoute) }}"
                        class="bg-[#3b82f6] text-white font-bold text-center py-3 rounded-xl"
                        >Dashboard</a
                    >
                @else
                    <a
                        href="{{ route('login') }}"
                        class="bg-[#3b82f6] text-white font-bold text-center py-3 rounded-xl"
                        >Masuk Layanan</a
                    >
                @endauth
            </div>
        </div>
    </div>
</nav>
