@php
    $authUser = auth()->user();
    $userName = $authUser->nama_lengkap ?? "RT";
    $userInitials = collect(explode(" ", trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode("");
@endphp

<nav class="fixed left-0 right-0 top-0 z-40 h-[91px] border border-black/10 bg-[#D8E7FF]">
    <div class="mx-auto flex h-full max-w-[1440px] items-center gap-4 px-4 sm:px-6 lg:px-[62px]">
        <button
            type="button"
            onclick="document.getElementById('rtSidebar').classList.toggle('hidden')"
            class="flex shrink-0 items-center justify-center rounded-[4px] border border-black/[0.08] bg-[#C9DEFF] p-1 text-[#141B34] transition hover:bg-[#B8D0F8]"
            aria-label="Buka menu RT"
        >
            <svg class="h-[30px] w-[30px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <a href="{{ route('rt.dashboard') }}" class="flex min-w-0 shrink-0 items-center gap-[11px]">
            <img
                src="{{ asset('images/logo-tegal.png') }}"
                alt="Logo Desa Dukuhtengah"
                class="h-[62px] w-[56px] object-contain"
            />
            <div class="min-w-0">
                <div class="text-[20px] font-extrabold leading-[23px] text-[#0F172A]">
                    <p class="mb-0">Desa</p>
                    <p>Dukuhtengah</p>
                </div>
                <p class="mt-1 text-[10px] font-medium uppercase tracking-[1px] text-[#64748B]">
                    Kecamatan Bojong
                </p>
            </div>
        </a>

        <div class="flex-1"></div>

        <div class="flex items-center gap-3 lg:gap-[29px]">
            <div class="hidden w-[303px] items-center gap-[17px] rounded-[26px] border border-[#F3F3F3] bg-[#F8FAFC] px-[19px] py-[13px] lg:flex">
                <svg class="h-6 w-6 shrink-0 text-black/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    type="text"
                    placeholder="Cari Layanan"
                    class="w-full border-none bg-transparent p-0 text-xs font-normal text-black/30 placeholder:text-black/30 focus:outline-none focus:ring-0"
                />
            </div>

            <button type="button" class="hidden text-[#141B34] transition hover:text-primary sm:inline-flex" aria-label="Notifikasi RT">
                <svg class="h-[39px] w-[39px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </button>

            <button type="button" class="hidden text-[#141B34] transition hover:text-primary md:inline-flex" aria-label="Pesan RT">
                <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75h6.75m-6.75 3h4.5m-8.25 6.375V6.375A2.625 2.625 0 017.5 3.75h9A2.625 2.625 0 0119.125 6.375v7.5A2.625 2.625 0 0116.5 16.5h-5.69l-3.936 3.28A.375.375 0 016.25 19.5v-3A2.625 2.625 0 013.625 13.875z" />
                </svg>
            </button>

            <div class="relative shrink-0">
                <button
                    type="button"
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="flex h-[57px] w-[57px] items-center justify-center rounded-full bg-white/50 text-sm font-bold text-[#141B34] shadow-[0_10px_30px_-22px_rgba(15,23,42,0.6)] transition hover:ring-2 hover:ring-white/70"
                    aria-label="Menu profil RT"
                >
                    <span>{{ $userInitials ?: "RT" }}</span>
                </button>

                <div class="absolute right-0 top-[68px] z-50 hidden w-56 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl">
                    <div class="px-4 py-3">
                        <p class="text-sm font-bold text-[#0F172A]">{{ $userName }}</p>
                        <p class="mt-1 text-xs text-[#94A3B8]">{{ $authUser->email ?? "" }}</p>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <a href="{{ route('rt.profil') }}" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-[#0F172A] transition hover:bg-[#F8FAFC]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profil RT
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-medium text-red-500 transition hover:bg-red-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

<div id="rtSidebar" class="hidden fixed left-0 top-0 z-50 h-full w-[280px] border-r border-[#E5EEF9] bg-white shadow-xl">
    <div class="flex h-[91px] items-center justify-between border-b border-[#EEF2F7] px-6">
        <a href="{{ route('rt.dashboard') }}" class="flex items-center gap-3">
            <img
                src="{{ asset('images/logo-tegal.png') }}"
                alt="Logo Desa Dukuhtengah"
                class="h-[48px] w-[44px] object-contain"
            />
            <div>
                <p class="text-sm font-extrabold leading-tight text-[#0F172A]">Desa Dukuhtengah</p>
                <p class="text-[10px] font-medium uppercase tracking-[1px] text-[#64748B]">Kecamatan Bojong</p>
            </div>
        </a>

        <button
            type="button"
            onclick="document.getElementById('rtSidebar').classList.add('hidden')"
            class="rounded-lg p-1 text-gray-400 transition hover:bg-gray-50 hover:text-gray-600"
            aria-label="Tutup menu RT"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="space-y-1 px-4 py-5">
        <a href="{{ route('rt.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.dashboard') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Dashboard
        </a>

        <a href="{{ route('rt.monografi.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.monografi.*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25A2.25 2.25 0 016 3h7.5a2.25 2.25 0 012.25 2.25v15a.75.75 0 01-1.28.53l-2.72-2.72a.75.75 0 00-1.06 0l-2.72 2.72a.75.75 0 01-1.28-.53v-15z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5h3.75M8.25 11.25h3.75" />
            </svg>
            Monografi
        </a>

        <a href="{{ route('rt.ajukan.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.ajukan.*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Ajukan Layanan
        </a>

        <a href="{{ route('rt.riwayat.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.riwayat.*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat & Status
        </a>

        <a href="{{ route('rt.profil') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.profil*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            Profil RT
        </a>
    </nav>
</div>

<div
    id="rtSidebarOverlay"
    class="hidden fixed inset-0 z-40 bg-black/20"
    onclick="document.getElementById('rtSidebar').classList.add('hidden'); this.classList.add('hidden');"
></div>

<script>
    const rtSidebar = document.getElementById("rtSidebar");
    const rtOverlay = document.getElementById("rtSidebarOverlay");

    if (rtSidebar && rtOverlay) {
        const rtObserver = new MutationObserver(() => {
            rtOverlay.classList.toggle(
                "hidden",
                rtSidebar.classList.contains("hidden"),
            );
        });

        rtObserver.observe(rtSidebar, {
            attributes: true,
            attributeFilter: ["class"],
        });
    }
</script>
