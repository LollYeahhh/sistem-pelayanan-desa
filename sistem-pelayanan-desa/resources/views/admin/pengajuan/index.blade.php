@extends('layouts.admin')
@section('title', 'Daftar Pengajuan Surat Masuk')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">Daftar Pengajuan Surat Masuk</h1>
    <form method="GET" action="{{ route('admin.pengajuan.index') }}" class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/nik" class="w-full sm:w-[350px] bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[70px]">No</th>
                    <th class="text-left text-sm font-semibold text-gray-500 px-6 py-4">Nama Pemohon</th>
                    <th class="text-left text-sm font-semibold text-gray-500 px-6 py-4">Jenis Layanan</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[140px]">Tanggal</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[130px]">Status</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[130px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuan as $i => $item)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="text-center text-sm text-gray-500 px-6 py-5">{{ $pengajuan->firstItem() + $i }}</td>
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
                    <td class="text-center px-6 py-5 text-sm text-gray-500">{{ $item->created_at->format('d M Y') }}</td>
                    <td class="text-center px-6 py-5">
                        @php
                            $colors = [
                                'menunggu' => 'bg-amber-50 text-amber-600',
                                'diproses' => 'bg-blue-50 text-blue-600',
                                'disetujui' => 'bg-green-50 text-green-600',
                                'selesai' => 'bg-emerald-50 text-emerald-600',
                                'ditolak' => 'bg-red-50 text-red-500',
                            ];
                        @endphp
                        <span class="inline-block text-xs font-bold px-3 py-1 rounded-full {{ $colors[$item->status] ?? 'bg-gray-50 text-gray-500' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="text-center px-6 py-5">
                        <a href="{{ route('admin.pengajuan.show', $item) }}" class="text-sm font-semibold text-primary hover:underline">Tinjau Surat</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-sm text-gray py-12">Belum ada pengajuan aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">Menampilkan {{ $pengajuan->firstItem() ?? 0 }} - {{ $pengajuan->lastItem() ?? 0 }} dari {{ $pengajuan->total() }} data</p>
        <div class="flex gap-2">
            @if(!$pengajuan->onFirstPage())<a href="{{ $pengajuan->previousPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Prev</a>@endif
            @if($pengajuan->hasMorePages())<a href="{{ $pengajuan->nextPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Next</a>@endif
        </div>
    </div>
</div>
@endsection
