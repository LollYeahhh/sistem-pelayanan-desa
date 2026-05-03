@extends ("layouts.rt")
@section ("title", "Riwayat RT")

@section ("content")
    <div
        class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <h1 class="text-2xl font-extrabold text-[#0F172A]">
                Riwayat Permohonan RT
            </h1>
            <p class="mt-1 text-sm text-[#64748B]">Pantau status seluruh permohonan surat yang diajukan akun RT.</p>
        </div>
        <a
            href="{{ route('rt.ajukan.index') }}"
            class="inline-flex items-center gap-2 self-start rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-primary/30 transition hover:bg-primary-hover"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Ajukan Baru
        </a>
    </div>
    <div class="mb-6 flex items-center gap-2 overflow-x-auto">
        <a
            href="{{ route('rt.riwayat.index') }}"
            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition {{ !request('status') ? 'bg-primary text-white shadow-md shadow-primary/30' : 'border border-gray-200 bg-white text-[#64748B] hover:bg-gray-50' }}"
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
                href="{{ route('rt.riwayat.index', ['status' => $key]) }}"
                class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition {{ request('status') === $key ? 'bg-primary text-white shadow-md shadow-primary/30' : 'border border-gray-200 bg-white text-[#64748B] hover:bg-gray-50' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F8FAFC]">
                        <th
                            class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-primary"
                        >
                            No. Registrasi
                        </th>
                        <th
                            class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-primary"
                        >
                            Jenis Layanan
                        </th>
                        <th
                            class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-primary"
                        >
                            Tanggal
                        </th>
                        <th
                            class="px-6 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-primary"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-primary"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permohonan as $p)
                        <tr
                            class="border-t border-gray-50 transition hover:bg-gray-50/50"
                        >
                            <td
                                class="px-6 py-4 text-sm font-bold text-primary"
                            >
                                {{ $p->no_registrasi }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm font-medium text-[#0F172A]"
                            >
                                {{
                                    $p->layanan->nama_layanan ??
                                        "-"
                                }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#64748B]">
                                {{
                                    $p->created_at->translatedFormat(
                                        "d M Y",
                                    )
                                }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-block rounded-full px-3 py-1 text-[11px] font-bold {{ $p->status_badge['color'] }}"
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
                                    href="{{ route('rt.riwayat.show', $p) }}"
                                    class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-6 py-16 text-center text-sm text-[#64748B]"
                            >
                                Belum ada riwayat permohonan RT.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-50 sm:hidden">
            @forelse ($permohonan as $p)
                <a
                    href="{{ route('rt.riwayat.show', $p) }}"
                    class="block px-4 py-4 transition hover:bg-gray-50"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <span
                            class="text-sm font-bold text-primary"
                            >{{ $p->no_registrasi }}</span
                        >
                        <span
                            class="rounded-full px-3 py-1 text-[11px] font-bold {{ $p->status_badge['color'] }}"
                        >
                            {{
                                $p->status_badge[
                                        "label"
                                    ]
                            }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-[#0F172A]">{{
                        $p->layanan->nama_layanan ??
                            "-"
                    }}</p>
                    <p class="mt-1 text-xs text-[#94A3B8]">{{
                        $p->created_at->translatedFormat(
                            "d M Y",
                        )
                    }}</p>
                </a>
            @empty
                <div class="py-16 text-center text-sm text-[#64748B]">
                    Belum ada riwayat permohonan RT.
                </div>
            @endforelse
        </div>
    </div>
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
