@extends('layouts.admin')
@section('title', 'Arsip Dokumen Pelayanan')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">Arsip Dokumen Pelayanan</h1>
    <button class="h-[50px] px-5 border border-gray-200 bg-white text-dark font-semibold text-sm rounded-xl flex items-center gap-2 hover:bg-gray-50 transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        Export Laporan
    </button>
</div>

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.arsip.index') }}" class="p-6 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/nik pemohon" class="w-full bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
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

    {{-- Table --}}
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
