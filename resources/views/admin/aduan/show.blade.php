@extends('layouts.admin')
@section('title', 'Detail Aduan Masyarakat')
@section('content')

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.aduan.index') }}" class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-500 transition hover:bg-gray-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-dark lg:text-[24px]">Detail Aduan Masyarakat</h1>
            <p class="mt-1 text-sm text-gray">Tinjau laporan warga dan perbarui status penanganannya.</p>
        </div>
    </div>

    <span class="inline-block rounded-full border px-4 py-2 text-xs font-bold {{ $aduan->status_badge_class }}">
        {{ $aduan->status_label }}
    </span>
</div>

@if (session('success'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-hidden rounded-2xl border border-gray-light bg-white">
    <div class="border-b border-gray-100 p-6">
        <div class="mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" />
            </svg>
            <h3 class="text-base font-bold text-dark">Data Pengirim</h3>
        </div>

        <div class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
            @foreach ([
                'Nama Lengkap' => $aduan->full_name,
                'Email' => $aduan->email,
                'Tanggal Aduan' => $aduan->created_at->format('d M Y, H:i'),
                'Status Saat Ini' => $aduan->status_label,
            ] as $label => $value)
                <div>
                    <p class="mb-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                    <div class="rounded-t-lg border-b border-gray-200 bg-[#F8FAFC] px-4 py-2.5">
                        <p class="text-sm text-dark">{{ $value }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="border-b border-gray-100 p-6">
        <div class="mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h9m-9 3h5.25M6.75 3.75h10.5A2.25 2.25 0 0119.5 6v12a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V6a2.25 2.25 0 012.25-2.25z" />
            </svg>
            <h3 class="text-base font-bold text-dark">Isi Aduan</h3>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-[#F8FAFC] p-5">
            <p class="whitespace-pre-line text-sm leading-7 text-dark">{{ $aduan->isi_aduan }}</p>
        </div>
    </div>

    <div class="border-b border-gray-100 p-6">
        <div class="mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <h3 class="text-base font-bold text-dark">Lampiran</h3>
        </div>

        @if ($aduan->lampiran_path)
            <div class="flex flex-col gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-dark">{{ basename($aduan->lampiran_path) }}</p>
                    <p class="mt-1 text-xs text-gray">Lampiran warga tersimpan dan siap diunduh.</p>
                </div>
                <a href="{{ route('admin.aduan.lampiran', $aduan) }}" class="inline-flex h-[44px] items-center justify-center rounded-xl bg-primary px-5 text-sm font-semibold text-white transition hover:bg-primary-hover">
                    Unduh Lampiran
                </a>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-gray-200 bg-[#F8FAFC] p-5 text-sm text-gray">
                Aduan ini dikirim tanpa lampiran.
            </div>
        @endif
    </div>

    <div class="p-6">
        <div class="mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h16.5M3.75 9.75h16.5m-16.5 5.25h16.5M3.75 20.25h16.5" />
            </svg>
            <h3 class="text-base font-bold text-dark">Perbarui Status</h3>
        </div>

        <form method="POST" action="{{ route('admin.aduan.status', $aduan) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            @csrf
            @method('PATCH')
            <div class="w-full sm:max-w-[240px]">
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-gray-500">Status Aduan</label>
                <select name="status" class="h-[50px] w-full rounded-xl border border-[#F3F3F3] bg-[#F8FAFC] px-4 text-sm text-gray-700 focus:border-primary focus:ring-primary">
                    <option value="baru" {{ $aduan->status === 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="diproses" {{ $aduan->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ $aduan->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <button type="submit" class="inline-flex h-[50px] items-center justify-center rounded-xl bg-primary px-6 text-sm font-semibold text-white transition hover:bg-primary-hover">
                Simpan Status
            </button>
        </form>
    </div>
</div>
@endsection
