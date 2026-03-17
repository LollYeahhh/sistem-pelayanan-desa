{{-- Navbar Admin - sesuai desain Figma Desktop-63 --}}
<nav
    class="fixed top-0 left-0 right-0 z-40 bg-[#d8e7ff] border border-black/10 h-[91px] overflow-hidden"
>
    <div class="flex items-center h-full px-6 lg:px-[62px]">
        {{-- Hamburger Button - dari Figma: bg-[#c9deff] border rounded-[4px] --}}
        <button
            onclick="
                document
                    .getElementById('adminSidebar')
                    .classList.toggle('hidden')
            "
            class="bg-[#c9deff] border border-black/[0.08] rounded-[4px] p-[4px] flex items-center justify-center shrink-0 hover:bg-[#b8d0f8] transition mr-4"
        >
            <svg class="w-[30px] h-[30px] text-[#0f172a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        {{-- Logo + Nama Desa --}}
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 shrink-0"
        >
            <img
                src="{{ asset('images/logo-tegal.png') }}"
                alt="Logo"
                class="h-[62px] w-[56px] object-contain"
            />
            <div class="flex flex-col gap-1">
                <div
                    class="font-extrabold text-[#0f172a] text-[20px] leading-[23px]"
                >
                    <p class="mb-0">Desa</p>
                    <p>Dukuhtengah</p>
                </div>
                <p class="text-[10px] font-medium text-[#64748b] tracking-[1px] uppercase leading-[15px]">Kecamatan Bojong</p>
            </div>
        </a>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Right: Search + Notif + Profile --}}
        <div class="flex items-center gap-8">
            {{-- Search Bar - dari Figma: bg-[#f8fafc] border-[#f3f3f3] rounded-[26px] w-[303px] px-[19px] py-[13px] --}}
            <div
                class="hidden md:flex bg-[#f8fafc] border border-[#f3f3f3] rounded-[26px] px-[19px] py-[13px] items-center gap-[17px] w-[303px]"
            >
                <svg class="w-6 h-6 text-black/30 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    type="text"
                    placeholder="Cari Data"
                    class="bg-transparent border-none outline-none text-[12px] font-normal text-black/30 placeholder-black/30 w-full"
                />
            </div>

            {{-- Notifikasi - dari Figma: size-[39px] dengan titik merah --}}
            <button
                class="relative shrink-0 w-[39px] h-[39px] flex items-center justify-center"
            >
                <svg class="w-6 h-6 text-[#0f172a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span
                    class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-[#d8e7ff]"
                ></span>
            </button>

            {{-- Profile Avatar - dari Figma: size-[57px] rounded-full --}}
            <div class="relative shrink-0">
                <button
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="w-[57px] h-[57px] rounded-full bg-gray-200 flex items-center justify-center overflow-hidden hover:ring-2 hover:ring-blue-300 transition"
                >
                    <svg class="w-[39px] h-[39px] text-gray-400 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </button>
                {{-- Dropdown --}}
                <div
                    class="hidden absolute right-0 top-[65px] bg-white border border-gray-100 rounded-xl shadow-lg py-2 w-52 z-50"
                >
                    <div class="px-4 py-2">
                        <p class="text-sm font-bold text-[#0f172a]">{{
                            auth()->user()->nama_lengkap ??
                                "Admin"
                        }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{
                            auth()->user()->email ??
                                ""
                        }}</p>
                    </div>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Sidebar --}}
<div
    id="adminSidebar"
    class="hidden fixed top-0 left-0 z-50 h-full w-[280px] bg-white border-r border-gray-100 shadow-lg"
>
    {{-- Sidebar Header --}}
    <div
        class="flex items-center justify-between px-6 h-[91px] border-b border-gray-100"
    >
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3"
        >
            <img
                src="{{ asset('images/logo-tegal.png') }}"
                alt="Logo"
                class="h-[48px] w-[44px] object-contain"
            />
            <div>
                <p class="text-sm font-extrabold text-[#0f172a] leading-tight">Desa Dukuhtengah</p>
                <p class="text-[10px] font-medium text-[#64748b] uppercase tracking-[1px]">Kecamatan Bojong</p>
            </div>
        </a>
        <button
            onclick="
                document.getElementById('adminSidebar').classList.add('hidden')
            "
            class="text-gray-400 hover:text-gray-600 p-1"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Menu --}}
    <nav class="px-4 py-4 space-y-1">
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                  {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" />
            </svg>
            Dashboard
        </a>

        <a
            href="{{ route('admin.layanan.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('admin.layanan.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Manajemen Layanan
        </a>

        <a
            href="{{ route('admin.warga.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('admin.warga.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            Database Warga
        </a>

        <a
            href="{{ route('admin.pengajuan.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                  {{ request()->routeIs('admin.pengajuan.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.981l7.5-4.039a2.25 2.25 0 012.134 0l7.5 4.039a2.25 2.25 0 011.183 1.98V19.5z" />
            </svg>
            Pengajuan Surat
        </a>

        <a
            href="{{ route('admin.arsip.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                  {{ request()->routeIs('admin.arsip.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            Arsip Dokumen
        </a>

        <a
            href="{{ route('admin.monografi.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                  {{ request()->routeIs('admin.monografi.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            Laporan Monografi
        </a>
    </nav>
</div>

{{-- Overlay --}}
<div
    id="sidebarOverlay"
    class="hidden fixed inset-0 bg-black/20 z-40"
    onclick="
        document.getElementById('adminSidebar').classList.add('hidden');
        this.classList.add('hidden');
    "
></div>

<script>
    const sidebar = document.getElementById("adminSidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const observer = new MutationObserver(() => {
        overlay.classList.toggle(
            "hidden",
            sidebar.classList.contains("hidden"),
        );
    });
    observer.observe(sidebar, { attributes: true, attributeFilter: ["class"] });
</script>
