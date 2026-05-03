@extends ("layouts.rt")
@section ("title", "Detail Permohonan RT - " . $permohonan->no_registrasi)

@section ("content")
    <a
        href="{{ route('rt.riwayat.index') }}"
        class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-[#64748B] transition hover:text-[#0F172A]"
    >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Riwayat
    </a>
    <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 lg:p-8">
        @php($masaBerlakuSurat = $permohonan->masa_berlaku_surat)
        <div
            class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-xl font-extrabold text-[#0F172A]">
                    {{
                        $permohonan->layanan->nama_layanan ??
                            "-"
                    }}
                </h1>
                <p class="mt-1 text-sm text-[#64748B]">
                    No. Registrasi:
                    <span
                        class="font-bold text-primary"
                        >{{ $permohonan->no_registrasi }}</span
                    >
                </p>
            </div>
            <span
                class="inline-block self-start rounded-full px-4 py-2 text-xs font-bold {{ $permohonan->status_badge['color'] }}"
            >
                {{
                    $permohonan->status_badge[
                            "label"
                        ]
                }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm lg:grid-cols-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-[#94A3B8]">Tanggal Diajukan</p>
                <p class="mt-1 font-bold text-[#0F172A]">{{
                    $permohonan->created_at->translatedFormat(
                        "d M Y",
                    )
                }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-[#94A3B8]">Waktu</p>
                <p class="mt-1 font-bold text-[#0F172A]">{{
                    $permohonan->created_at->format(
                        "H:i",
                    )
                }} WIB</p>
            </div>
            @if ($permohonan->reviewed_at)
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#94A3B8]">Ditinjau Pada</p>
                    <p class="mt-1 font-bold text-[#0F172A]">{{
                        $permohonan->reviewed_at->translatedFormat(
                            "d M Y",
                        )
                    }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#94A3B8]">Ditinjau Oleh</p>
                    <p class="mt-1 font-bold text-[#0F172A]">{{
                        $permohonan->reviewer->nama_lengkap ??
                            "-"
                    }}</p>
                </div>
                @if ($permohonan->status === 'selesai' && $permohonan->file_dokumen_path && $masaBerlakuSurat)
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#94A3B8]">Masa Berlaku Surat</p>
                        <p class="mt-1 font-bold text-[#0F172A]">{{ $masaBerlakuSurat->translatedFormat("d M Y") }}</p>
                    </div>
                @endif
            @endif
        </div>

        @if ($permohonan->status === 'ditolak' && $permohonan->rejection_reason)
            <div class="mt-4 rounded-xl border border-red-100 bg-red-50 p-4">
                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-red-600">Alasan Penolakan</p>
                <p class="text-sm text-red-700">{{ $permohonan->rejection_reason }}</p>
            </div>
        @endif

        @if ($permohonan->status === 'selesai' && $permohonan->file_dokumen_path)
            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Dokumen Hasil</p>
                        <p class="mt-1 text-sm font-semibold text-emerald-900">
                            {{ $permohonan->file_dokumen_nama ?: 'Dokumen hasil tersedia untuk diunduh' }}
                        </p>
                        <p class="mt-1 text-sm text-emerald-800">
                            {{ optional($permohonan->dokumen_selesai_at)->translatedFormat("d M Y, H:i") ?? optional($permohonan->reviewed_at)->translatedFormat("d M Y, H:i") ?? "-" }} WIB
                        </p>
                    </div>
                    <a
                        href="{{ route('pengajuan.dokumen.download', $permohonan) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-hover"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4.5-4.5M12 15l-4.5-4.5M4.5 19.5h15" />
                        </svg>
                        Download Dokumen
                    </a>
                </div>

                @if ($permohonan->catatan_admin)
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-white/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Catatan Admin</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-emerald-900">{{ $permohonan->catatan_admin }}</p>
                    </div>
                @endif
            </div>
        @elseif ($permohonan->catatan_admin)
            <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
                <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-blue-700">Catatan Admin</p>
                <p class="text-sm text-blue-900 whitespace-pre-line">{{ $permohonan->catatan_admin }}</p>
            </div>
        @endif

        @if ($permohonan->status === 'ditolak')
            <div class="mt-4">
                @if ($permohonan->layanan?->is_active)
                    <a
                        href="{{ route('rt.ajukan.form', $permohonan->layanan) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-hover"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajukan Ulang
                    </a>
                @else
                    <div class="inline-flex rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        Layanan ini sedang tidak tersedia untuk diajukan ulang.
                    </div>
                @endif
            </div>
        @endif
    </div>
    <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 lg:p-8">
        <h2 class="mb-5 text-base font-bold text-[#0F172A]">Data Permohonan</h2>
        <div class="space-y-4">
            @foreach ($permohonan->data as $item)
                <div
                    class="flex flex-col gap-1 border-b border-gray-50 pb-4 last:border-0 last:pb-0 sm:flex-row sm:items-start sm:gap-4"
                >
                    <p class="shrink-0 text-sm font-semibold text-[#64748B] sm:w-48">{{
                        $item->field->label ??
                            "Field"
                    }}</p>
                    @if ($item->field && $item->field->tipe_input === "file")
                        <a
                            href="{{ asset('storage/' . $item->value) }}"
                            target="_blank"
                            class="text-sm font-medium text-primary hover:underline"
                        >
                            Lihat Lampiran
                        </a>
                    @else
                        <p class="text-sm text-[#0F172A]">{{ $item->value ?? "-" }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @if ($permohonan->lampiran->count() > 0)
        <div class="rounded-2xl border border-gray-100 bg-white p-6 lg:p-8">
            <h2 class="mb-5 text-base font-bold text-[#0F172A]">
                Lampiran Dokumen
            </h2>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($permohonan->lampiran as $lampiran)
                    <a
                        href="{{ asset('storage/' . $lampiran->file_path) }}"
                        target="_blank"
                        class="flex items-center gap-3 rounded-xl border border-gray-200 bg-[#F8FAFC] p-4 transition hover:border-primary/40 hover:bg-primary/5"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-[#0F172A]">{{ $lampiran->nama_dokumen }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ strtoupper(pathinfo($lampiran->file_path, PATHINFO_EXTENSION)) }} - {{ $lampiran->created_at->translatedFormat("d M Y") }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
