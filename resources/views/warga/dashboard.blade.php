@extends ("layouts.warga")
@section ("title", "Dashboard Warga")

@php
    $user = $user ?? auth()->user();
    $layananPreview =
        $layananPreview ??
        \App\Models\Layanan::where("is_active", true)
            ->orderBy("id")
            ->take(6)
            ->get();
    $permohonan =
        $permohonan ??
        \App\Models\PermohonanSurat::where("user_id", $user->id)
            ->with("layanan")
            ->latest()
            ->take(2)
            ->get();

    $hour = \Illuminate\Support\Carbon::now("Asia/Jakarta")->hour;
    $greeting =
        $hour < 12
            ? "Pagi"
            : ($hour < 15
                ? "Siang"
                : ($hour < 18
                    ? "Sore"
                    : "Malam"));
    $firstName = collect(explode(" ", trim($user->nama_lengkap ?? "Warga")))
        ->filter()
        ->first();

    $canAjukan = $user->is_verified && $user->status_akun === "aktif";
    $statusLabel = !$user->is_verified
        ? "Menunggu Verifikasi"
        : ($user->status_akun === "aktif"
            ? "Aktif"
            : "Nonaktif");
    $statusClass = !$user->is_verified
        ? "text-amber-600"
        : ($user->status_akun === "aktif"
            ? "text-emerald-600"
            : "text-red-500");
    $ajukanLockedMessage = !$user->is_verified
        ? "Menunggu verifikasi admin"
        : "Akun sedang nonaktif";

    $statusMap = [
        "selesai" => [
            "label" => "Selesai",
            "pill" => "bg-[#F3F4F6] text-emerald-600",
            "icon" => "bg-[#E8FFF2]",
            "icon_text" => "text-emerald-600",
        ],
        "diproses" => [
            "label" => "Diproses",
            "pill" => "bg-[#EFF6FF] text-[#2563EB]",
            "icon" => "bg-[#EAF2FF]",
            "icon_text" => "text-[#2563EB]",
        ],
        "menunggu" => [
            "label" => "Menunggu",
            "pill" => "bg-[#FFFBEB] text-amber-600",
            "icon" => "bg-[#FFF7DB]",
            "icon_text" => "text-amber-500",
        ],
        "ditolak" => [
            "label" => "Ditolak",
            "pill" => "bg-[#FEF2F2] text-red-600",
            "icon" => "bg-[#FEE2E2]",
            "icon_text" => "text-red-500",
        ],
    ];
@endphp

@section ("content")
    <div class="space-y-8 lg:space-y-10">
        <section
            class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between"
        >
            <div class="max-w-2xl">
                <h1
                    class="text-[30px] font-extrabold leading-[1.2] text-[#111827]"
                >
                    Halo Selamat {{ $greeting }}, {{ $firstName ?? "Warga" }}<span
                        class="ml-1"
                        >👋</span
                    >
                </h1>
                <p class="mt-2 text-base font-medium leading-6 text-[#6B7280]">
                    Status kependudukan Anda:
                    <span class="font-bold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </p>
            </div>

            @if ($canAjukan)
                <a
                    href="{{ route('warga.ajukan.index') }}"
                    class="inline-flex h-[50px] items-center gap-3 self-start rounded-[11px] bg-primary px-[15px] py-[9px] text-base font-semibold text-white shadow-[0_12px_30px_-18px_rgba(37,99,235,0.9)] transition hover:bg-primary-hover"
                >
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/20 bg-white/10"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </span>
                    Ajukan Surat
                </a>
            @else
                <span
                    class="inline-flex h-[50px] cursor-not-allowed items-center gap-3 self-start rounded-[11px] bg-[#CBD5E1] px-[15px] py-[9px] text-base font-semibold text-white"
                >
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/20 bg-white/10"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </span>
                    Ajukan Surat
                </span>
            @endif
        </section>

        <section class="space-y-4 lg:space-y-5">
            <div class="flex items-center justify-end">
                @if ($canAjukan)
                    <a
                        href="{{ route('warga.ajukan.index') }}"
                        class="text-base font-semibold leading-7 text-primary transition hover:text-primary-hover"
                    >
                        Lihat Selengkapnya
                    </a>
                @else
                    <span
                        class="text-base font-semibold leading-7 text-[#94A3B8]"
                    >
                        {{ $ajukanLockedMessage }}
                    </span>
                @endif
            </div>

            @if ($layananPreview->isNotEmpty())
                <div
                    class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6"
                >
                    @foreach ($layananPreview as $item)
                        @if ($canAjukan)
                            <a
                                href="{{ route('warga.ajukan.form', $item) }}"
                                class="group flex flex-col items-center"
                            >


                        @else
                            <div class="flex flex-col items-center opacity-60">
                        @endif
                        <div
                            class="bg-white rounded-[11px] w-[140px] h-[140px] lg:w-[158px] lg:h-[158px] flex items-center justify-center shadow-sm transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-md"
                        >
                            @if ($item->icon_path)
                                <img
                                    src="{{ asset('images/layanan/' . $item->icon_path) }}"
                                    alt="{{ $item->nama_layanan }}"
                                    class="w-20 h-20 lg:w-24 lg:h-24 object-contain group-hover:scale-110 transition-transform duration-300"
                                />
                            @else
                                <div
                                    class="w-[100px] h-[96px] bg-[#e0edff] rounded-2xl flex items-center justify-center"
                                >
                                    <svg class="w-10 h-10 text-[#2563eb] group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <p
                            class="text-[14px] font-medium text-black text-center mt-2 leading-[28px] w-[140px] lg:w-[178px]"
                        >
                            {{ $item->nama_layanan }}
                        </p>
                        @if ($canAjukan)
                            </a>
                        @else
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div
                    class="rounded-[24px] bg-white px-6 py-10 text-center shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#EAF2FF] text-primary"
                    >
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-[#111827]">Belum ada layanan aktif yang bisa diajukan.</p>
                </div>
            @endif
        </section>

        <section
            class="overflow-hidden rounded-[24px] bg-white shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]"
        >
            <div
                class="flex items-center justify-between gap-4 border-b border-[#F3F4F6] px-5 py-6 sm:px-7"
            >
                <h2 class="text-[20px] font-extrabold leading-7 text-[#1F2937]">
                    Progres Permohonan
                </h2>
                <a
                    href="{{ route('warga.riwayat.index') }}"
                    class="text-base font-semibold text-primary transition hover:text-primary-hover"
                >
                    Semua Riwayat
                </a>
            </div>

            <div class="space-y-4 px-4 py-5 sm:px-6 sm:py-6">
                @forelse ($permohonan as $p)
                    @php
                        $status = $statusMap[$p->status] ?? [
                            "label" => ucfirst($p->status),
                            "pill" => "bg-[#F3F4F6] text-[#6B7280]",
                            "icon" => "bg-[#F3F4F6]",
                            "icon_text" => "text-[#6B7280]",
                        ];
                    @endphp
                    <a
                        href="{{ route('warga.riwayat.show', $p) }}"
                        class="flex flex-col gap-4 rounded-[11px] bg-[#F8FAFC] px-4 py-5 transition hover:bg-[#F3F7FD] sm:flex-row sm:items-center sm:justify-between sm:px-6"
                    >
                        <div class="min-w-0 flex items-center gap-4">
                            <div
                                class="flex h-[42px] w-[42px] items-center justify-center rounded-full {{ $status['icon'] }}"
                            >
                                @if ($p->layanan?->icon_path)
                                    <img
                                        src="{{ asset('images/layanan/' . $p->layanan->icon_path) }}"
                                        alt="{{ $p->layanan->nama_layanan }}"
                                        class="h-6 w-6 object-contain"
                                    />
                                @elseif ($p->status === "selesai")
                                    <svg
                                        class="h-5 w-5 {{ $status['icon_text'] }}"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($p->status === "diproses")
                                    <svg
                                        class="h-5 w-5 {{ $status['icon_text'] }}"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                                    </svg>
                                @elseif ($p->status === "ditolak")
                                    <svg
                                        class="h-5 w-5 {{ $status['icon_text'] }}"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg
                                        class="h-5 w-5 {{ $status['icon_text'] }}"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p
                                    class="truncate text-base font-bold leading-7 text-black"
                                >
                                    {{
                                        $p->layanan->nama_layanan ??
                                            "-"
                                    }}
                                </p>
                                <p
                                    class="text-[10px] font-extrabold uppercase leading-[15px] tracking-[0.04em] text-[#9CA3AF]"
                                >
                                    #{{ strtoupper($p->no_registrasi) }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="inline-flex self-start rounded-full px-4 py-1 text-[10px] font-extrabold uppercase leading-[15px] {{ $status['pill'] }} sm:self-center"
                        >
                            {{ $status["label"] }}
                        </span>
                    </a>
                @empty
                    <div
                        class="rounded-[11px] bg-[#F8FAFC] px-6 py-10 text-center"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#EAF2FF] text-primary"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-[#111827]">Belum ada permohonan surat yang bisa ditampilkan.</p>
                        @if ($canAjukan)
                            <a
                                href="{{ route('warga.ajukan.index') }}"
                                class="mt-3 inline-flex text-sm font-semibold text-primary hover:text-primary-hover"
                            >
                                Ajukan surat sekarang
                            </a>
                        @else
                            <p class="mt-3 text-sm font-semibold text-[#94A3B8]">
                                {{
                                    !$user->is_verified
                                        ? "Ajukan layanan tersedia setelah admin memverifikasi akun."
                                        : "Ajukan layanan dinonaktifkan sementara oleh admin."
                                }}
                            </p>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
