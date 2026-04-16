@extends('layouts.admin')
@section('title', 'Arsip Dokumen Pelayanan')
@section('content')

@php
    $exportYear = $exportYear ?? (int) now('Asia/Jakarta')->format('Y');
    $selectedMonths = collect($selectedMonths ?? [now('Asia/Jakarta')->month])->map(fn($month) => (int) $month)->all();
    $monthOptions = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
    $selectedMonthLabels = collect($selectedMonths)
        ->map(fn($month) => $monthOptions[$month] ?? null)
        ->filter()
        ->values();
    $selectedMonthSummary = match (true) {
        $selectedMonthLabels->isEmpty() => 'Pilih bulan',
        $selectedMonthLabels->count() === 1 => $selectedMonthLabels->first(),
        default => $selectedMonthLabels->count() . ' bulan dipilih',
    };
@endphp

<div class="mb-6 flex flex-col gap-4">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">Arsip Dokumen Pelayanan</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola arsip dokumen pelayanan dan export laporan Excel per bulan proses.</p>
        </div>
        <button
            type="button"
            onclick="openArsipExportModal()"
            class="inline-flex h-[50px] items-center justify-center gap-2 rounded-xl bg-dark px-5 text-sm font-semibold text-white transition hover:bg-slate-800"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Export Laporan
        </button>
    </div>
</div>

<div id="arsipExportModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" onclick="closeArsipExportModal()"></div>

    <div class="relative z-10 w-full max-w-4xl overflow-visible rounded-[28px] border border-white/70 bg-white shadow-[0_32px_80px_-36px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-5 sm:px-7">
            <div class="max-w-2xl">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-gray-400">Export Arsip</p>
                <h2 class="mt-2 text-2xl font-extrabold text-dark">Download laporan Excel per multi-bulan</h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Laporan diambil dari tanggal proses <span class="font-semibold text-dark">reviewed_at</span> dan menyertakan metadata dokumen hasil upload, bukan file lampiran fisik.
                </p>
            </div>

            <button
                type="button"
                onclick="closeArsipExportModal()"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 text-gray-400 transition hover:bg-gray-50 hover:text-gray-600"
                aria-label="Tutup modal export arsip"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ route('admin.arsip.export') }}" class="overflow-visible px-6 py-6 sm:px-7">
            <div class="grid gap-4 xl:grid-cols-[180px_1fr]">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.08em] text-gray-400">Tahun</label>
                    <input
                        type="number"
                        name="tahun"
                        min="2000"
                        max="2100"
                        value="{{ old('tahun', request('tahun', $exportYear)) }}"
                        class="h-[54px] w-full rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-dark focus:border-primary focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-[0.08em] text-gray-400">Pilih Bulan</label>
                    <div class="relative overflow-visible" data-month-dropdown>
                        <button
                            type="button"
                            class="flex h-[54px] w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-dark transition hover:border-gray-300 focus:border-primary focus:outline-none"
                            data-month-dropdown-trigger
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span data-month-dropdown-label>{{ $selectedMonthSummary }}</span>
                            <svg class="h-5 w-5 text-gray-400 transition" data-month-dropdown-icon fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            class="absolute left-0 z-20 mt-2 hidden w-full overflow-y-auto rounded-2xl border border-gray-200 bg-white p-3 shadow-[0_24px_50px_-28px_rgba(15,23,42,0.45)]"
                            data-month-dropdown-panel
                        >
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach($monthOptions as $monthValue => $monthLabel)
                                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-semibold text-dark transition hover:border-primary/20 hover:bg-primary/5">
                                        <input
                                            type="checkbox"
                                            name="bulan[]"
                                            value="{{ $monthValue }}"
                                            class="rounded border-gray-300 text-primary focus:ring-primary"
                                            data-month-checkbox
                                            data-month-label="{{ $monthLabel }}"
                                            {{ in_array($monthValue, $selectedMonths, true) ? 'checked' : '' }}
                                        >
                                        <span>{{ $monthLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                <button
                    type="button"
                    onclick="closeArsipExportModal()"
                    class="inline-flex h-[50px] items-center justify-center rounded-xl border border-gray-200 bg-white px-5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                >
                    Batal
                </button>
                <button class="inline-flex h-[50px] items-center justify-center gap-2 rounded-xl bg-dark px-5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Export Excel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    <form method="GET" action="{{ route('admin.arsip.index') }}" class="p-6 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK pemohon" class="w-full bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-500">TGL:</span>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="bg-white border border-gray-200 rounded-full h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
            </div>
            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-full h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
                <option value="">Semua</option>
                <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Diterima</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[70px]">No</th>
                    <th class="text-left text-sm font-semibold text-gray-500 px-6 py-4">Nama Pemohon</th>
                    <th class="text-left text-sm font-semibold text-gray-500 px-6 py-4">Jenis Layanan</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[140px]">Tanggal Proses</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[120px]">Status</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[120px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($arsip as $i => $item)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="text-center text-sm text-gray-500 px-6 py-5">{{ $arsip->firstItem() + $i }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary shrink-0">
                                {{ strtoupper(substr($item->user->nama_lengkap ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $item->user->nama_lengkap ?? '')[1] ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-dark">{{ $item->user->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-gray">{{ $item->user->nik ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-sm text-dark">{{ $item->layanan->nama_layanan ?? '-' }}</td>
                    <td class="text-center px-6 py-5 text-sm text-gray-500">{{ $item->reviewed_at ? \Carbon\Carbon::parse($item->reviewed_at)->format('d M Y') : '-' }}</td>
                    <td class="text-center px-6 py-5">
                        <span class="inline-block text-xs font-bold px-3 py-1 rounded-full {{
                            $item->status === 'selesai'
                                ? 'bg-emerald-50 text-emerald-600'
                                : ($item->status === 'disetujui' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500')
                        }}">
                            {{
                                $item->status === 'selesai'
                                    ? 'Selesai'
                                    : ($item->status === 'disetujui' ? 'Diterima' : 'Ditolak')
                            }}
                        </span>
                    </td>
                    <td class="text-center px-6 py-5">
                        <div class="flex items-center justify-center gap-3">
                            <a
                                href="{{ route('admin.pengajuan.show', $item) }}"
                                title="Tinjau"
                                aria-label="Tinjau dokumen"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-primary/15 bg-primary/5 text-primary transition hover:border-primary/30 hover:bg-primary/10"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75A3.75 3.75 0 1012 8.25a3.75 3.75 0 000 7.5z" />
                                </svg>
                            </a>

                            @if($item->status === 'selesai' && $item->file_dokumen_path)
                                <a
                                    href="{{ route('pengajuan.dokumen.download', $item) }}"
                                    title="Unduh"
                                    aria-label="Unduh dokumen hasil"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-100"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4.5-4.5M12 15l-4.5-4.5M4.5 19.5h15" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-sm text-gray py-12">Belum ada arsip dokumen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">Menampilkan {{ $arsip->count() }} data</p>
        <div class="flex gap-2">
            @if(!$arsip->onFirstPage())<a href="{{ $arsip->previousPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Prev</a>@endif
            @if($arsip->hasMorePages())<a href="{{ $arsip->nextPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Next</a>@endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openArsipExportModal() {
        const modal = document.getElementById('arsipExportModal');

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeArsipExportModal() {
        const modal = document.getElementById('arsipExportModal');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const exportModal = document.getElementById('arsipExportModal');

        if (exportModal && window.location.search.includes('tahun=')) {
            closeArsipExportModal();
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeArsipExportModal();
            }
        });

        document.querySelectorAll('[data-month-dropdown]').forEach((dropdown) => {
            const trigger = dropdown.querySelector('[data-month-dropdown-trigger]');
            const panel = dropdown.querySelector('[data-month-dropdown-panel]');
            const label = dropdown.querySelector('[data-month-dropdown-label]');
            const icon = dropdown.querySelector('[data-month-dropdown-icon]');
            const checkboxes = Array.from(dropdown.querySelectorAll('[data-month-checkbox]'));

            if (!trigger || !panel || !label || checkboxes.length === 0) {
                return;
            }

            const updateLabel = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked);

                if (selected.length === 0) {
                    label.textContent = 'Pilih bulan';
                    return;
                }

                if (selected.length === 1) {
                    label.textContent = selected[0].dataset.monthLabel || 'Pilih bulan';
                    return;
                }

                label.textContent = `${selected.length} bulan dipilih`;
            };

            const positionDropdown = () => {
                panel.style.maxHeight = '';
                panel.style.top = '';
                panel.style.bottom = '';
                panel.style.marginTop = '';
                panel.style.marginBottom = '';

                const triggerRect = trigger.getBoundingClientRect();
                const viewportPadding = 24;
                const panelGap = 8;
                const spaceBelow = window.innerHeight - triggerRect.bottom - viewportPadding;
                const spaceAbove = triggerRect.top - viewportPadding;
                const shouldOpenUpward = spaceBelow < 260 && spaceAbove > spaceBelow;
                const availableSpace = Math.max(
                    shouldOpenUpward ? spaceAbove : spaceBelow,
                    140
                );

                if (shouldOpenUpward) {
                    panel.style.bottom = `calc(100% + ${panelGap}px)`;
                    panel.style.top = 'auto';
                    panel.style.marginTop = '0';
                    panel.style.marginBottom = '0';
                } else {
                    panel.style.top = `calc(100% + ${panelGap}px)`;
                    panel.style.bottom = 'auto';
                    panel.style.marginTop = '0';
                    panel.style.marginBottom = '0';
                }

                panel.style.maxHeight = `${Math.min(availableSpace, 420)}px`;
            };

            const closeDropdown = () => {
                panel.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
                icon.classList.remove('rotate-180');
            };

            const openDropdown = () => {
                positionDropdown();
                panel.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                icon.classList.add('rotate-180');
            };

            trigger.addEventListener('click', () => {
                if (panel.classList.contains('hidden')) {
                    document.querySelectorAll('[data-month-dropdown-panel]').forEach((otherPanel) => {
                        if (otherPanel !== panel) {
                            otherPanel.classList.add('hidden');
                        }
                    });
                    document.querySelectorAll('[data-month-dropdown-trigger]').forEach((otherTrigger) => {
                        if (otherTrigger !== trigger) {
                            otherTrigger.setAttribute('aria-expanded', 'false');
                        }
                    });
                    document.querySelectorAll('[data-month-dropdown-icon]').forEach((otherIcon) => {
                        if (otherIcon !== icon) {
                            otherIcon.classList.remove('rotate-180');
                        }
                    });

                    openDropdown();
                    return;
                }

                closeDropdown();
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateLabel);
            });

            document.addEventListener('click', (event) => {
                if (!dropdown.contains(event.target)) {
                    closeDropdown();
                }
            });

            window.addEventListener('resize', () => {
                if (!panel.classList.contains('hidden')) {
                    positionDropdown();
                }
            });

            updateLabel();
        });
    });
</script>
@endpush
