@extends ("layouts.warga")
@section ("title", "Detail Permohonan - " . $permohonan->no_registrasi)
@section ("page-title", "Detail Permohonan")

@section ("content")
    {{-- Back --}}
    <a
        href="{{ route('warga.riwayat.index') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748b] hover:text-[#0f172a] transition mb-6"
    >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Kembali ke Riwayat
    </a>
    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 mb-6">
        @php($masaBerlakuSurat = $permohonan->masa_berlaku_surat)
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
        >
            <div>
                <h1 class="text-xl font-extrabold text-[#0f172a]">
                    {{
                        $permohonan->layanan->nama_layanan ??
                            "-"
                    }}
                </h1>
                <p class="text-sm text-[#64748b] mt-1">No. Registrasi: <span class="font-bold text-primary">{{ $permohonan->no_registrasi }}</span></p>
            </div>
            <span
                class="inline-block self-start text-xs font-bold px-4 py-2 rounded-full {{ $permohonan->status_badge['color'] }}"
            >
                {{
                    $permohonan->status_badge[
                            "label"
                        ]
                }}
            </span>
        </div>

        {{-- Timeline --}}
        <div class="grid grid-cols-2 gap-4 text-sm lg:grid-cols-5">
            <div>
                <p class="text-[11px] font-semibold text-[#94a3b8] uppercase tracking-wider">Tanggal Diajukan</p>
                <p class="font-bold text-[#0f172a] mt-1">{{
                    $permohonan->created_at->translatedFormat(
                        "d M Y",
                    )
                }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-[#94a3b8] uppercase tracking-wider">Waktu</p>
                <p class="font-bold text-[#0f172a] mt-1">{{
                    $permohonan->created_at->format(
                        "H:i",
                    )
                }} WIB</p>
            </div>
            @if ($permohonan->reviewed_at)
                <div>
                    <p class="text-[11px] font-semibold text-[#94a3b8] uppercase tracking-wider">Ditinjau Pada</p>
                    <p class="font-bold text-[#0f172a] mt-1">{{
                        $permohonan->reviewed_at->translatedFormat(
                            "d M Y",
                        )
                    }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-[#94a3b8] uppercase tracking-wider">Ditinjau Oleh</p>
                    <p class="font-bold text-[#0f172a] mt-1">{{
                        $permohonan->reviewer->nama_lengkap ??
                            "-"
                    }}</p>
                </div>
                @if ($permohonan->status === 'selesai' && $permohonan->file_dokumen_path && $masaBerlakuSurat)
                    <div>
                        <p class="text-[11px] font-semibold text-[#94a3b8] uppercase tracking-wider">Masa Berlaku Surat</p>
                        <p class="font-bold text-[#0f172a] mt-1">{{ $masaBerlakuSurat->translatedFormat("d M Y") }}</p>
                    </div>
                @endif
            @endif
        </div>

        @if ($permohonan->status === 'ditolak' && $permohonan->rejection_reason)
            <div class="mt-4 bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-1">Alasan Penolakan</p>
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
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Catatan Admin</p>
                <p class="mt-1 whitespace-pre-line text-sm text-blue-900">{{ $permohonan->catatan_admin }}</p>
            </div>
        @endif

        @if ($permohonan->status === 'ditolak')
            <div class="mt-4">
                @if ($permohonan->layanan?->is_active)
                    <a
                        href="{{ route('warga.ajukan.form', $permohonan->layanan) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-hover"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
    {{-- Data Permohonan --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 mb-6">
        <h2 class="text-base font-bold text-[#0f172a] mb-5">Data Permohonan</h2>

        <div class="space-y-4">
            @foreach ($permohonan->data as $item)
                <div
                    class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4 pb-4 border-b border-gray-50 last:border-0 last:pb-0"
                >
                    <p class="text-sm font-semibold text-[#64748b] sm:w-48 shrink-0">{{
                        $item->field->label ??
                            "Field"
                    }}</p>
                    @if ($item->field && $item->field->tipe_input === "file")
                        <a
                            href="{{ asset('storage/' . $item->value) }}"
                            target="_blank"
                            class="text-sm font-medium text-primary hover:underline flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                            </svg>
                            Lihat Lampiran
                        </a>
                    @else
                        <p class="text-sm text-[#0f172a]">{{ $item->value ?? "-" }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    {{-- Lampiran --}}
    @if ($permohonan->lampiran->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8">
            <h2 class="text-base font-bold text-[#0f172a] mb-5">
                Lampiran Dokumen
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($permohonan->lampiran as $lampiran)
                    <a
                        href="{{ asset('storage/' . $lampiran->file_path) }}"
                        target="_blank"
                        class="flex items-center gap-3 bg-[#f8fafc] border border-gray-200 rounded-xl p-4 hover:border-primary/40 hover:bg-primary/5 transition group"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"
                        >
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-[#0f172a] truncate group-hover:text-primary transition">{{ $lampiran->nama_dokumen }}</p>
                            <p class="text-xs text-[#94a3b8]">{{ strtoupper(pathinfo($lampiran->file_path, PATHINFO_EXTENSION)) }} · {{ $lampiran->created_at->translatedFormat("d M Y") }}</p>
                        </div>
                        <svg class="w-5 h-5 text-[#94a3b8] group-hover:text-primary transition ml-auto shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

@endsection
