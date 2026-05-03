@php
    $authUser = auth()->user();
    $userName = $authUser->nama_lengkap ?? "RT";
    $userInitials = collect(explode(" ", trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode("");
@endphp

<nav
    class="fixed left-0 right-0 top-0 z-40 h-[83px] border border-black/10 bg-[#D8E7FF]"
>
    <div
        class="mx-auto flex h-full max-w-[1440px] items-center gap-4 px-4 sm:px-6 lg:px-[62px]"
    >
        <button
            type="button"
            onclick="
                document.getElementById('rtSidebar').classList.toggle('hidden')
            "
            class="flex shrink-0 items-center justify-center rounded-[4px] border border-black/[0.08] bg-[#C9DEFF] p-[3px] text-[#141B34] transition hover:bg-[#B8D0F8]"
            aria-label="Buka menu RT"
        >
            <svg class="h-[28px] w-[28px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <a
            href="{{ route('rt.dashboard') }}"
            class="flex min-w-0 shrink-0 items-center gap-[11px]"
        >
            <img
                src="{{ asset('images/logo-tegal.png') }}"
                alt="Logo Desa Dukuhtengah"
                class="h-[56px] w-[50px] object-contain"
            />
            <div class="min-w-0">
                <div
                    class="text-[18px] font-extrabold leading-[21px] text-[#0F172A]"
                >
                    <p class="mb-0">Desa</p>
                    <p>Dukuhtengah</p>
                </div>
                <p class="mt-1 text-[10px] font-medium uppercase tracking-[1px] text-[#64748B]">Kecamatan Bojong</p>
            </div>
        </a>

        <div class="flex-1"></div>

        <div class="flex items-center gap-3 lg:gap-[29px]">
            <div class="relative hidden lg:block" id="rtSearchWrapper">
                <div
                    class="flex w-[303px] items-center gap-[17px] rounded-[26px] border border-[#F3F3F3] bg-[#F8FAFC] px-[19px] py-[10px]"
                >
                    <svg class="h-[22px] w-[22px] shrink-0 text-black/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input
                        id="rtSearchInput"
                        type="text"
                        placeholder="Cari Layanan"
                        autocomplete="off"
                        class="w-full border-none bg-transparent p-0 text-xs font-normal text-[#0f172a] placeholder:text-black/30 focus:outline-none focus:ring-0"
                    />
                    <svg id="rtSearchSpinner" class="hidden h-4 w-4 shrink-0 animate-spin text-black/30" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                {{-- Search Results Dropdown --}}
                <div
                    id="rtSearchResults"
                    class="absolute left-0 top-[48px] z-50 hidden w-[360px] max-h-[400px] overflow-y-auto rounded-2xl border border-gray-100 bg-white shadow-xl"
                >
                    <div id="rtSearchList" class="py-2"></div>
                    <div
                        id="rtSearchEmpty"
                        class="hidden px-4 py-6 text-center text-sm text-gray-400"
                    >
                        Tidak ada hasil ditemukan
                    </div>
                </div>
            </div>

            <div
                class="relative hidden shrink-0 sm:block"
                id="rtNotificationWrapper"
            >
                <button
                    type="button"
                    id="rtNotificationButton"
                    aria-expanded="false"
                    class="relative flex h-[35px] w-[35px] items-center justify-center text-[#141B34] transition hover:text-primary"
                >
                    <svg class="h-[35px] w-[35px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span
                        id="rtNotificationDot"
                        class="hidden absolute right-1 top-1 h-2 w-2 rounded-full bg-red-500"
                    ></span>
                </button>

                <div
                    id="rtNotificationDropdown"
                    class="absolute right-0 top-[46px] z-50 hidden w-[360px] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl"
                >
                    <div class="flex items-start justify-between border-b border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-[#0F172A]">Notifikasi</p>
                            <p
                                id="rtNotificationCount"
                                class="mt-0.5 text-xs text-gray-400"
                            >
                                Memuat notifikasi...
                            </p>
                        </div>
                        <button
                            type="button"
                            id="rtNotificationReadAll"
                            class="hidden text-xs font-semibold text-primary hover:underline"
                        >
                            Tandai semua dibaca
                        </button>
                    </div>

                    <div
                        id="rtNotificationEmpty"
                        class="hidden px-5 py-8 text-center text-sm text-gray-400"
                    >
                        Belum ada notifikasi terbaru.
                    </div>

                    <div
                        id="rtNotificationList"
                        class="max-h-[360px] overflow-y-auto divide-y divide-gray-100"
                    ></div>
                </div>
            </div>

            <button
                type="button"
                class="hidden text-[#141B34] transition hover:text-primary md:inline-flex"
                aria-label="Pesan RT"
            >
                <svg class="h-[34px] w-[34px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75h6.75m-6.75 3h4.5m-8.25 6.375V6.375A2.625 2.625 0 017.5 3.75h9A2.625 2.625 0 0119.125 6.375v7.5A2.625 2.625 0 0116.5 16.5h-5.69l-3.936 3.28A.375.375 0 016.25 19.5v-3A2.625 2.625 0 013.625 13.875z" />
                </svg>
            </button>

            <div class="relative shrink-0">
                <button
                    type="button"
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="flex h-[52px] w-[52px] items-center justify-center rounded-full bg-white/50 text-sm font-bold text-[#141B34] shadow-[0_10px_30px_-22px_rgba(15,23,42,0.6)] transition hover:ring-2 hover:ring-white/70"
                    aria-label="Menu profil RT"
                >
                    <span>{{ $userInitials ?: "RT" }}</span>
                </button>

                <div
                    class="absolute right-0 top-[60px] z-50 hidden w-56 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl"
                >
                    <div class="px-4 py-3">
                        <p class="text-sm font-bold text-[#0F172A]">{{ $userName }}</p>
                        <p class="mt-1 text-xs text-[#94A3B8]">{{
                            $authUser->email ??
                                ""
                        }}</p>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <a
                        href="{{ route('rt.profil') }}"
                        class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-[#0F172A] transition hover:bg-[#F8FAFC]"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profil RT
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-medium text-red-500 transition hover:bg-red-50"
                        >
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

<div
    id="rtSidebar"
    class="hidden fixed left-0 top-0 z-50 h-full w-[280px] border-r border-[#E5EEF9] bg-white shadow-xl"
>
    <div
        class="flex h-[83px] items-center justify-between border-b border-[#EEF2F7] px-6"
    >
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
            onclick="
                document.getElementById('rtSidebar').classList.add('hidden')
            "
            class="rounded-lg p-1 text-gray-400 transition hover:bg-gray-50 hover:text-gray-600"
            aria-label="Tutup menu RT"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="space-y-1 px-4 py-5">
        <a
            href="{{ route('rt.dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.dashboard') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Dashboard
        </a>

        <a
            href="{{ route('rt.monografi.index') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.monografi.*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25A2.25 2.25 0 016 3h7.5a2.25 2.25 0 012.25 2.25v15a.75.75 0 01-1.28.53l-2.72-2.72a.75.75 0 00-1.06 0l-2.72 2.72a.75.75 0 01-1.28-.53v-15z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5h3.75M8.25 11.25h3.75" />
            </svg>
            Monografi
        </a>

        <a
            href="{{ route('rt.ajukan.index') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.ajukan.*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Ajukan Layanan
        </a>

        <a
            href="{{ route('rt.riwayat.index') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.riwayat.*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat & Status
        </a>

        <a
            href="{{ route('rt.profil') }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('rt.profil*') ? 'bg-primary/10 text-primary' : 'text-[#64748B] hover:bg-[#F8FAFC]' }}"
        >
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
    onclick="
        document.getElementById('rtSidebar').classList.add('hidden');
        this.classList.add('hidden');
    "
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

<script>
    (function () {
        const wrapper = document.getElementById("rtNotificationWrapper");
        const button = document.getElementById("rtNotificationButton");
        const dropdown = document.getElementById("rtNotificationDropdown");
        const dot = document.getElementById("rtNotificationDot");
        const count = document.getElementById("rtNotificationCount");
        const list = document.getElementById("rtNotificationList");
        const empty = document.getElementById("rtNotificationEmpty");
        const readAllButton = document.getElementById("rtNotificationReadAll");

        if (!wrapper || !button || !dropdown) return;

        const indexUrl = "{{ route('rt.notifications.index') }}";
        const readAllUrl = "{{ route('rt.notifications.read-all') }}";
        const readUrlTemplate =
            "{{ route('rt.notifications.read', ['notification' => '__ID__']) }}";
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") ?? "";

        const iconMap = {
            status_disetujui:
                '<svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m6 2.25a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            status_selesai:
                '<svg class="h-5 w-5 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6.75L9 14.25l-3-3m13.5-5.25a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            status_ditolak:
                '<svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
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
                knownUnreadIds = new Set();
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
        const input = document.getElementById("rtSearchInput");
        const wrapper = document.getElementById("rtSearchWrapper");
        const results = document.getElementById("rtSearchResults");
        const list = document.getElementById("rtSearchList");
        const empty = document.getElementById("rtSearchEmpty");
        const spinner = document.getElementById("rtSearchSpinner");

        if (!input) return;

        let debounceTimer = null;
        const searchUrl = "{{ route('rt.search') }}";

        const typeIcons = {
            Layanan:
                '<svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>',
            Riwayat:
                '<svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        };

        const typeBadgeColors = {
            Layanan: "bg-green-50 text-green-600",
            Riwayat: "bg-blue-50 text-blue-600",
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
