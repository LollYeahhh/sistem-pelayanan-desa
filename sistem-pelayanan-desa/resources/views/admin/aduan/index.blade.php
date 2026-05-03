@extends('layouts.admin')
@section('title', 'Kelola Aduan Masyarakat')
@section('content')

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-[28px] font-extrabold text-dark lg:text-[32px]">Kelola Aduan Masyarakat</h1>
        <p class="mt-1 text-sm text-gray">Tinjau aduan yang masuk dari landing page dan perbarui status penanganannya.</p>
    </div>

    <form method="GET" action="{{ route('admin.aduan.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative">
            <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau isi aduan" class="h-[50px] w-full rounded-full border border-gray-200 bg-white pl-12 pr-4 text-sm placeholder-gray-400 focus:border-primary focus:ring-primary sm:w-[320px]">
        </div>
        <select name="status" onchange="this.form.submit()" class="h-[50px] rounded-full border border-gray-200 bg-white px-4 text-sm text-gray-600 focus:border-primary focus:ring-primary">
            <option value="">Semua Status</option>
            <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-light bg-white">
    <div class="hidden overflow-x-auto md:block">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="w-[70px] px-6 py-4 text-center text-sm font-semibold text-gray-500">No</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-500">Pengirim</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-500">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-500">Ringkasan Aduan</th>
                    <th class="w-[140px] px-6 py-4 text-center text-sm font-semibold text-gray-500">Tanggal</th>
                    <th class="w-[130px] px-6 py-4 text-center text-sm font-semibold text-gray-500">Status</th>
                    <th class="w-[120px] px-6 py-4 text-center text-sm font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($aduan as $index => $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                        <td class="px-6 py-5 text-center text-sm text-gray-500">{{ $aduan->firstItem() + $index }}</td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                    {{ strtoupper(substr($item->nama_depan ?? 'A', 0, 1)) }}{{ strtoupper(substr($item->nama_belakang ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{ $item->full_name }}</p>
                                    <p class="text-xs text-gray">{{ $item->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-dark">{{ $item->email }}</td>
                        <td class="px-6 py-5 text-sm text-gray">{{ \Illuminate\Support\Str::limit($item->isi_aduan, 70) }}</td>
                        <td class="px-6 py-5 text-center text-sm text-gray-500">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-block rounded-full border px-3 py-1 text-xs font-bold {{ $item->status_badge_class }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('admin.aduan.show', $item) }}" class="text-sm font-semibold text-primary hover:underline">Detail Aduan</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray">Belum ada aduan masyarakat yang masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="space-y-3 px-4 py-4 md:hidden">
        @forelse ($aduan as $item)
            <div class="rounded-xl border border-gray-100 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-dark">{{ $item->full_name }}</p>
                        <p class="mt-1 text-xs text-gray">{{ $item->email }}</p>
                        <p class="mt-1 text-xs text-gray">{{ $item->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="shrink-0 rounded-full border px-3 py-1 text-[11px] font-bold {{ $item->status_badge_class }}">
                        {{ $item->status_label }}
                    </span>
                </div>
                <p class="mt-3 text-sm text-gray">{{ \Illuminate\Support\Str::limit($item->isi_aduan, 110) }}</p>
                <a href="{{ route('admin.aduan.show', $item) }}" class="mt-4 inline-flex text-sm font-semibold text-primary hover:underline">Detail Aduan</a>
            </div>
        @empty
            <p class="py-8 text-center text-sm text-gray">Belum ada aduan masyarakat yang masuk.</p>
        @endforelse
    </div>

    <div class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-500">Menampilkan {{ $aduan->firstItem() ?? 0 }} - {{ $aduan->lastItem() ?? 0 }} dari {{ $aduan->total() }} data</p>
        <div class="flex gap-2">
            @if (!$aduan->onFirstPage())
                <a href="{{ $aduan->previousPageUrl() }}" class="rounded-lg bg-gray-100 px-4 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200">Prev</a>
            @endif
            @if ($aduan->hasMorePages())
                <a href="{{ $aduan->nextPageUrl() }}" class="rounded-lg bg-gray-100 px-4 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200">Next</a>
            @endif
        </div>
    </div>
</div>
@endsection
