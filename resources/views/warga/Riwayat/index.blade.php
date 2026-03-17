@extends ("layouts.warga")
@section ("title", "Riwayat & Status")
@section ("page-title", "Riwayat & Status")

@section ("content")
    {{-- Header --}}
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
    >
        <div>
            <h1 class="text-2xl font-extrabold text-[#0f172a]">
                Riwayat Permohonan
            </h1>
            <p class="text-sm text-[#64748b] mt-1">Pantau status seluruh permohonan surat Anda</p>
        </div>
        <a
            href="{{ route('warga.ajukan.index') }}"
            class="inline-flex items-center gap-2 bg-primary text-white font-bold text-sm px-5 py-2.5 rounded-xl hover:bg-primary-hover transition shadow-md shadow-primary/30 self-start"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Ajukan Baru
        </a>
    </div>
    {{-- Filter --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto">
        <a
            href="{{ route('warga.riwayat.index') }}"
            class="px-4 py-2 rounded-full text-sm font-semibold transition whitespace-nowrap
            {{ !request('status') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'bg-white text-[#64748b] border border-gray-200 hover:bg-gray-50' }}"
        >
            Semua
        </a>
        @foreach ([
                "menunggu" => "Menunggu",
                "diproses" => "Diproses",
                "disetujui" => "Disetujui",
                "selesai" => "Selesai",
                "ditolak" => "Ditolak",
            ]
            as $key => $label)
            <a
                href="{{ route('warga.riwayat.index', ['status' => $key]) }}"
                class="px-4 py-2 rounded-full text-sm font-semibold transition whitespace-nowrap
                {{ request('status') === $key ? 'bg-primary text-white shadow-md shadow-primary/30' : 'bg-white text-[#64748b] border border-gray-200 hover:bg-gray-50' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>
    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#f8fafc]">
                        <th
                            class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3"
                        >
                            No. Registrasi
                        </th>
                        <th
                            class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3"
                        >
                            Jenis Layanan
                        </th>
                        <th
                            class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3"
                        >
                            Tanggal
                        </th>
                        <th
                            class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3"
                        >
                            Status
                        </th>
                        <th
                            class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permohonan as $p)
                        <tr
                            class="border-t border-gray-50 hover:bg-gray-50/50 transition"
                        >
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-bold text-primary"
                                    >{{ $p->no_registrasi }}</span
                                >
                            </td>
                            <td
                                class="px-6 py-4 text-sm font-medium text-[#0f172a]"
                            >
                                {{
                                    $p->layanan->nama_layanan ??
                                        "-"
                                }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#64748b]">
                                {{
                                    $p->created_at->translatedFormat(
                                        "d M Y",
                                    )
                                }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-block text-[11px] font-bold px-3 py-1 rounded-full {{ $p->status_badge['color'] }}"
                                >
                                    {{
                                        $p->status_badge[
                                                "label"
                                            ]
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a
                                    href="{{ route('warga.riwayat.show', $p) }}"
                                    class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline"
                                >
                                    Detail
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
                                >
                                    <svg class="w-8 h-8 text-[#94a3b8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-[#64748b]">Belum ada riwayat permohonan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="sm:hidden divide-y divide-gray-50">
            @forelse ($permohonan as $p)
                <a
                    href="{{ route('warga.riwayat.show', $p) }}"
                    class="block px-4 py-4 hover:bg-gray-50 transition"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-sm font-bold text-primary"
                            >{{ $p->no_registrasi }}</span
                        >
                        <span
                            class="text-[11px] font-bold px-3 py-1 rounded-full {{ $p->status_badge['color'] }}"
                        >
                            {{
                                $p->status_badge[
                                        "label"
                                    ]
                            }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-[#0f172a]">{{
                        $p->layanan->nama_layanan ??
                            "-"
                    }}</p>
                    <p class="text-xs text-[#94a3b8] mt-1">{{
                        $p->created_at->translatedFormat(
                            "d M Y",
                        )
                    }}</p>
                </a>
            @empty
                <div class="py-16 text-center">
                    <p class="text-sm text-[#64748b]">Belum ada riwayat permohonan.</p>
                </div>
            @endforelse
        </div>
    </div>
    {{-- Pagination --}}
    @if ($permohonan->hasPages())
        <div class="mt-6">
            {{
                $permohonan
                    ->withQueryString()
                    ->links()
            }}
        </div>
    @endif

@endsection
