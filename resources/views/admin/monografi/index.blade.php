@extends('layouts.admin')
@section('title', 'Laporan Monografi Wilayah')
@section('content')

@php
    $tahun = $tahun ?? (int) now('Asia/Jakarta')->format('Y');
    $totalFields = $totalFields ?? 0;
@endphp

<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">Laporan Monografi Wilayah</h1>
        <p class="mt-1 text-sm text-gray-500">Rekap pengisian monografi RT tahun {{ $tahun }}.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.monografi.form-builder') }}" class="h-[50px] px-5 bg-[#0F172A] text-white font-semibold text-sm rounded-xl flex items-center gap-2 hover:bg-[#1E293B] transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            Kelola Form Monografi
        </a>
        <button type="button" class="h-[50px] px-5 border border-gray-200 bg-white text-dark font-semibold text-sm rounded-xl flex items-center gap-2 hover:bg-gray-50 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Export Semua Data
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    <form method="GET" action="{{ route('admin.monografi.index') }}" class="p-6 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama RT atau wilayah" class="w-full bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
            </div>
            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-full h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
                <option value="">Semua Status</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="kosong" {{ request('status') === 'kosong' ? 'selected' : '' }}>Belum Isi</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[70px]">No</th>
                    <th class="text-left text-sm font-semibold text-gray-500 px-6 py-4">Nama RT</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[140px]">Wilayah</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[150px]">Update Terakhir</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[120px]">Status</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[120px]">Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $item)
                    @php
                        $filledCount = (int) ($item->filled_fields_count ?? 0);
                        $isConfigured = $totalFields > 0;
                        $isComplete = $isConfigured && $filledCount >= $totalFields;
                        $isDraft = $isConfigured && $filledCount > 0 && ! $isComplete;
                        $statusLabel = ! $isConfigured
                            ? 'Belum Setup'
                            : ($isComplete ? 'Selesai' : ($isDraft ? 'Draft' : 'Belum Isi'));
                        $statusClass = ! $isConfigured
                            ? 'bg-slate-100 text-slate-600'
                            : ($isComplete
                                ? 'bg-green-50 text-green-600'
                                : ($isDraft ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-600'));
                        $rtLabel = $item->rt_rw ?: '-';
                        $initials = collect(explode(' ', trim($item->nama_lengkap ?? 'RT')))
                            ->filter()
                            ->take(2)
                            ->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))
                            ->implode('');
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="text-center text-sm text-gray-500 px-6 py-5">{{ $data->firstItem() + $i }}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary shrink-0">
                                    {{ $initials ?: 'RT' }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{ $item->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-gray">{{ $item->nik ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center px-6 py-5 text-sm text-dark">{{ $rtLabel }}</td>
                        <td class="text-center px-6 py-5 text-sm text-gray-500">
                            {{ $item->last_monografi_update ? \Carbon\Carbon::parse($item->last_monografi_update)->format('d M Y') : '-' }}
                        </td>
                        <td class="text-center px-6 py-5">
                            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-center px-6 py-5 text-sm font-semibold text-dark">
                            {{ $isConfigured ? $filledCount . '/' . $totalFields . ' Field' : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-sm text-gray py-12">Belum ada data monografi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">Menampilkan {{ $data->count() }} data</p>
        <div class="flex gap-2">
            @if(!$data->onFirstPage())<a href="{{ $data->previousPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Prev</a>@endif
            @if($data->hasMorePages())<a href="{{ $data->nextPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Next</a>@endif
        </div>
    </div>
</div>
@endsection
