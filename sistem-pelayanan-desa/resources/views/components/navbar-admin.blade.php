{{-- Navbar Admin - sesuai desain Figma Desktop-63 --}}
<nav
    class="fixed top-0 left-0 right-0 z-40 border border-black/10 bg-[#d8e7ff] h-[83px]"
>
    <div class="flex h-full items-center px-6 lg:px-[62px]">
        {{-- Hamburger Button - dari Figma: bg-[#c9deff] border rounded-[4px] --}}
        <button
            onclick="
                document
                    .getElementById('adminSidebar')
                    .classList.toggle('hidden')
            "
            class="mr-4 flex shrink-0 items-center justify-center rounded-[4px] border border-black/[0.08] bg-[#c9deff] p-[3px] transition hover:bg-[#b8d0f8]"
        >
            <svg class="h-[28px] w-[28px] text-[#0f172a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                class="h-[56px] w-[50px] object-contain"
            />
            <div class="flex flex-col gap-1">
                <div
                    class="text-[18px] font-extrabold leading-[21px] text-[#0f172a]"
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
            {{-- Search Bar --}}
            <div class="relative hidden md:block" id="adminSearchWrapper">
                <div
                    class="flex w-[303px] items-center gap-[17px] rounded-[26px] border border-[#f3f3f3] bg-[#f8fafc] px-[19px] py-[10px]"
                >
                    <svg class="h-[22px] w-[22px] shrink-0 text-black/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input
                        id="adminSearchInput"
                        type="text"
                        placeholder="Cari Data"
                        autocomplete="off"
                        class="bg-transparent border-none outline-none text-[12px] font-normal text-[#0f172a] placeholder-black/30 w-full"
                    />
                    <svg id="adminSearchSpinner" class="hidden h-4 w-4 shrink-0 animate-spin text-black/30" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                {{-- Search Results Dropdown --}}
                <div
                    id="adminSearchResults"
                    class="absolute left-0 top-[48px] z-50 hidden w-[360px] max-h-[400px] overflow-y-auto rounded-2xl border border-gray-100 bg-white shadow-xl"
                >
                    <div id="adminSearchList" class="py-2"></div>
                    <div
                        id="adminSearchEmpty"
                        class="hidden px-4 py-6 text-center text-sm text-gray-400"
                    >
                        Tidak ada hasil ditemukan
                    </div>
                </div>
            </div>

            {{-- Notifikasi - dari Figma: size-[39px] dengan titik merah --}}
            <div class="relative shrink-0" id="adminNotificationWrapper">
                <button
                    type="button"
                    id="adminNotificationButton"
                    aria-expanded="false"
                    class="relative flex h-[35px] w-[35px] shrink-0 items-center justify-center rounded-full transition hover:bg-black/5"
                >
                    <svg class="h-[22px] w-[22px] text-[#0f172a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span
                        id="adminNotificationDot"
                        class="hidden absolute top-1 right-1 h-2 w-2 rounded-full border border-[#d8e7ff] bg-red-500"
                    ></span>
                </button>

                <div
                    id="adminNotificationDropdown"
                    class="absolute right-0 top-[46px] z-50 hidden w-[360px] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl"
                >
                    <div class="flex items-start justify-between border-b border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-[#0f172a]">Notifikasi</p>
                            <p id="adminNotificationCount" class="mt-0.5 text-xs text-gray-400">
                                Memuat notifikasi...
                            </p>
                        </div>
                        <button
                            type="button"
                            id="adminNotificationReadAll"
                            class="hidden text-xs font-semibold text-primary hover:underline"
                        >
                            Tandai semua dibaca
                        </button>
                    </div>

                    <div
                        id="adminNotificationEmpty"
                        class="hidden px-5 py-8 text-center text-sm text-gray-400"
                    >
                        Belum ada notifikasi terbaru.
                    </div>

                    <div
                        id="adminNotificationList"
                        class="max-h-[360px] overflow-y-auto divide-y divide-gray-100"
                    ></div>
                </div>
            </div>

            {{-- Profile Avatar - dari Figma: size-[57px] rounded-full --}}
            <div class="relative shrink-0">
                <button
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="flex h-[52px] w-[52px] items-center justify-center overflow-hidden rounded-full bg-gray-200 transition hover:ring-2 hover:ring-blue-300"
                >
                    <svg class="h-[34px] w-[34px] text-gray-400 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </button>
                {{-- Dropdown --}}
                <div
                    class="absolute right-0 top-[60px] z-50 hidden w-52 rounded-xl border border-gray-100 bg-white py-2 shadow-lg"
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
        class="flex h-[83px] items-center justify-between border-b border-gray-100 px-6"
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
            href="{{ route('admin.aduan.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                  {{ request()->routeIs('admin.aduan.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h6m-9 8.25h10.125c1.243 0 2.25-1.007 2.25-2.25V6.75c0-1.243-1.007-2.25-2.25-2.25H6.375c-1.243 0-2.25 1.007-2.25 2.25v13.5l3.375-2.25z" />
            </svg>
            Aduan Masyarakat
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

<script>
    (function () {
        const wrapper = document.getElementById("adminNotificationWrapper");
        const button = document.getElementById("adminNotificationButton");
        const dropdown = document.getElementById("adminNotificationDropdown");
        const dot = document.getElementById("adminNotificationDot");
        const count = document.getElementById("adminNotificationCount");
        const list = document.getElementById("adminNotificationList");
        const empty = document.getElementById("adminNotificationEmpty");
        const readAllButton = document.getElementById("adminNotificationReadAll");

        if (!wrapper || !button || !dropdown) return;

        const indexUrl = "{{ route('admin.notifications.index') }}";
        const readAllUrl = "{{ route('admin.notifications.read-all') }}";
        const readUrlTemplate =
            "{{ route('admin.notifications.read', ['notification' => '__ID__']) }}";
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ?? "";

        const iconMap = {
            pengajuan:
                '<svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51"/></svg>',
            aduan:
                '<svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h6m-9 8.25h10.125c1.243 0 2.25-1.007 2.25-2.25V6.75c0-1.243-1.007-2.25-2.25-2.25H6.375c-1.243 0-2.25 1.007-2.25 2.25v13.5l3.375-2.25z"/></svg>',
            aktivasi:
                '<svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
            umum:
                '<svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2.25"/></svg>',
        };

        let initialized = false;
        let knownUnreadIds = new Set();
        let audioReady = false;
        let soundUnlocked = false;
        const notificationSound = new Audio(
            "{{ asset('sounds/notification.mp3') }}",
        );
        notificationSound.preload = "auto";
        notificationSound.volume = 1;

        function escapeHtml(value) {
            const div = document.createElement("div");
            div.textContent = value ?? "";
            return div.innerHTML;
        }

        function setUnreadState(unreadCount) {
            dot.classList.toggle("hidden", unreadCount < 1);
            readAllButton.classList.toggle("hidden", unreadCount < 1);
            count.textContent =
                unreadCount > 0
                    ? unreadCount + " notifikasi belum dibaca"
                    : "Semua notifikasi sudah dibaca";
        }

        function renderNotifications(items) {
            list.innerHTML = "";
            empty.classList.toggle("hidden", items.length > 0);

            items.forEach(function (item) {
                const anchor = document.createElement("a");
                anchor.href = item.url;
                anchor.dataset.notificationId = item.id;
                anchor.className =
                    "flex gap-3 px-4 py-3 transition hover:bg-[#F8FAFC]";

                const icon = iconMap[item.kind] || iconMap.umum;
                const unreadDot = item.is_read
                    ? ""
                    : '<span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-red-500"></span>';

                anchor.innerHTML =
                    '<div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F1F5F9]">' +
                    icon +
                    "</div>" +
                    '<div class="min-w-0 flex-1">' +
                    '<div class="flex items-start gap-2">' +
                    '<p class="flex-1 text-sm font-semibold text-[#0f172a]">' +
                    escapeHtml(item.title) +
                    "</p>" +
                    unreadDot +
                    "</div>" +
                    '<p class="mt-1 text-xs leading-5 text-gray-500">' +
                    escapeHtml(item.message) +
                    "</p>" +
                    '<p class="mt-1 text-[11px] font-medium text-gray-400">' +
                    escapeHtml(item.created_at_label) +
                    "</p>" +
                    "</div>";

                list.appendChild(anchor);
            });
        }

        function unlockSound() {
            if (soundUnlocked) return;

            const previousMuted = notificationSound.muted;
            notificationSound.muted = true;
            notificationSound
                .play()
                .then(function () {
                    notificationSound.pause();
                    notificationSound.currentTime = 0;
                    notificationSound.muted = previousMuted;
                    soundUnlocked = true;
                    audioReady = true;
                })
                .catch(function () {
                    notificationSound.muted = previousMuted;
                    audioReady = false;
                });
        }

        function playBeep() {
            if (!audioReady) return;

            try {
                notificationSound.pause();
                notificationSound.currentTime = 0;
                notificationSound.play().catch(function () {
                    audioReady = false;
                    soundUnlocked = false;
                });
            } catch (error) {
                audioReady = false;
            }
        }

        function syncKnownUnread(items) {
            knownUnreadIds = new Set(
                items.filter((item) => !item.is_read).map((item) => item.id),
            );
        }

        function handlePayload(payload) {
            const items = Array.isArray(payload.items) ? payload.items : [];
            setUnreadState(payload.unread_count ?? 0);
            renderNotifications(items);

            const currentUnreadIds = new Set(
                items.filter((item) => !item.is_read).map((item) => item.id),
            );

            if (initialized) {
                const hasNewUnread = Array.from(currentUnreadIds).some(
                    (id) => !knownUnreadIds.has(id),
                );

                if (hasNewUnread) {
                    playBeep();
                }
            }

            knownUnreadIds = currentUnreadIds;
            initialized = true;
        }

        function postJson(url) {
            return fetch(url, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: "{}",
                keepalive: true,
            }).then((response) => response.json());
        }

        function fetchNotifications() {
            return fetch(indexUrl, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then(handlePayload)
                .catch(() => {});
        }

        function markAsRead(id) {
            return postJson(readUrlTemplate.replace("__ID__", id)).catch(
                () => {},
            );
        }

        button.addEventListener("click", function () {
            const isHidden = dropdown.classList.contains("hidden");
            dropdown.classList.toggle("hidden");
            button.setAttribute("aria-expanded", String(isHidden));

            if (isHidden) {
                fetchNotifications();
            }
        });

        readAllButton.addEventListener("click", function () {
            postJson(readAllUrl).then(() => {
                syncKnownUnread([]);
                fetchNotifications();
            });
        });

        list.addEventListener("click", function (event) {
            const link = event.target.closest("a[data-notification-id]");
            if (!link) return;

            event.preventDefault();

            const targetUrl = link.href;
            const notificationId = link.dataset.notificationId;

            markAsRead(notificationId).finally(function () {
                window.location.href = targetUrl;
            });
        });

        document.addEventListener("click", function (event) {
            if (!wrapper.contains(event.target)) {
                dropdown.classList.add("hidden");
                button.setAttribute("aria-expanded", "false");
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                dropdown.classList.add("hidden");
                button.setAttribute("aria-expanded", "false");
            }
        });

        ["click", "keydown", "touchstart"].forEach(function (eventName) {
            document.addEventListener(
                eventName,
                function () {
                    unlockSound();
                },
                { once: true },
            );
        });

        fetchNotifications();
        window.setInterval(fetchNotifications, 15000);
    })();
</script>

<script>
    (function () {
        const input = document.getElementById("adminSearchInput");
        const wrapper = document.getElementById("adminSearchWrapper");
        const results = document.getElementById("adminSearchResults");
        const list = document.getElementById("adminSearchList");
        const empty = document.getElementById("adminSearchEmpty");
        const spinner = document.getElementById("adminSearchSpinner");

        if (!input) return;

        let debounceTimer = null;
        const searchUrl = "{{ route('admin.search') }}";

        const typeIcons = {
            Warga: '<svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
            Layanan:
                '<svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>',
            Pengajuan:
                '<svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51"/></svg>',
            Aduan: '<svg class="h-4 w-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h6m-9 8.25h10.125c1.243 0 2.25-1.007 2.25-2.25V6.75c0-1.243-1.007-2.25-2.25-2.25H6.375c-1.243 0-2.25 1.007-2.25 2.25v13.5l3.375-2.25z"/></svg>',
        };

        const typeBadgeColors = {
            Warga: "bg-blue-50 text-blue-600",
            Layanan: "bg-green-50 text-green-600",
            Pengajuan: "bg-amber-50 text-amber-600",
            Aduan: "bg-red-50 text-red-500",
        };

        function escapeHtml(str) {
            const div = document.createElement("div");
            div.textContent = str;
            return div.innerHTML;
        }

        function renderResults(data) {
            list.innerHTML = "";
            empty.classList.add("hidden");

            if (data.length === 0) {
                empty.classList.remove("hidden");
                results.classList.remove("hidden");
                return;
            }

            data.forEach(function (item) {
                const icon = typeIcons[item.type] || typeIcons.Layanan;
                const badgeColor =
                    typeBadgeColors[item.type] || "bg-gray-50 text-gray-600";
                const a = document.createElement("a");
                a.href = item.url;
                a.className =
                    "flex items-center gap-3 px-4 py-2.5 hover:bg-[#F8FAFC] transition-colors";
                a.innerHTML =
                    '<div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#F1F5F9]">' +
                    icon +
                    "</div>" +
                    '<div class="min-w-0 flex-1">' +
                    '<p class="truncate text-sm font-semibold text-[#0f172a]">' +
                    escapeHtml(item.title) +
                    "</p>" +
                    '<p class="truncate text-xs text-gray-400 mt-0.5">' +
                    escapeHtml(item.sub) +
                    "</p>" +
                    "</div>" +
                    '<span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold ' +
                    badgeColor +
                    '">' +
                    escapeHtml(item.type) +
                    "</span>";
                list.appendChild(a);
            });

            results.classList.remove("hidden");
        }

        function doSearch() {
            const q = input.value.trim();
            if (q.length < 2) {
                results.classList.add("hidden");
                spinner.classList.add("hidden");
                return;
            }

            spinner.classList.remove("hidden");

            fetch(searchUrl + "?q=" + encodeURIComponent(q), {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((r) => r.json())
                .then((data) => {
                    spinner.classList.add("hidden");
                    renderResults(data);
                })
                .catch(() => {
                    spinner.classList.add("hidden");
                    results.classList.add("hidden");
                });
        }

        input.addEventListener("input", function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(doSearch, 300);
        });

        input.addEventListener("focus", function () {
            if (input.value.trim().length >= 2) doSearch();
        });

        document.addEventListener("click", function (e) {
            if (!wrapper.contains(e.target)) {
                results.classList.add("hidden");
            }
        });

        input.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                results.classList.add("hidden");
                input.blur();
            }
        });
    })();
</script>
