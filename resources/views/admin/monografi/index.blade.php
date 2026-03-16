@extends('layouts.admin')
@section('title', 'Laporan Monografi Wilayah')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">Laporan Monografi Wilayah</h1>
    <button class="h-[50px] px-5 border border-gray-200 bg-white text-dark font-semibold text-sm rounded-xl flex items-center gap-2 hover:bg-gray-50 transition">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        Export Semua Data
    </button>
</div>

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.monografi.index') }}" class="p-6 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama RT" class="w-full bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
            </div>
            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-full h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
                <option value="">Semua</option>
                <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Selesai</option>
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
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[140px]">Wilayah</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[150px]">Tanggal Selesai</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[120px]">Status</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $item)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="text-center text-sm text-gray-500 px-6 py-5">{{ $data->firstItem() + $i }}</td>
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
                    <td class="text-center px-6 py-5 text-sm text-dark">RT {{ $item->user->rt ?? '-' }}</td>
                    <td class="text-center px-6 py-5 text-sm text-gray-500">{{ $item->reviewed_at ? \Carbon\Carbon::parse($item->reviewed_at)->format('d M Y') : '-' }}</td>
                    <td class="text-center px-6 py-5">
                        <span class="inline-block text-xs font-bold px-3 py-1 rounded-full bg-green-50 text-green-600">Selesai</span>
                    </td>
                    <td class="text-center px-6 py-5">
                        <button class="text-gray-400 hover:text-gray-600" title="Cetak">
                            <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>
                        </button>
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