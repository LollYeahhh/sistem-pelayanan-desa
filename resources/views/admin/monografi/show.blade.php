@extends('layouts.admin')
@section('title', 'Tinjau Monografi RT')
@section('content')

@php
    $fieldValues = collect($values ?? []);
    $statusLabel = match ($status) {
        'submitted' => 'Sudah Dikirim',
        'draft' => 'Draft',
        default => 'Belum Isi',
    };
    $statusClass = match ($status) {
        'submitted' => 'bg-green-50 text-green-600',
        'draft' => 'bg-amber-50 text-amber-600',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <a href="{{ route('admin.monografi.index', ['tahun' => $tahun]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-hover">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                Kembali ke laporan monografi
            </a>
            <h1 class="mt-3 text-[28px] lg:text-[32px] font-extrabold text-dark">Tinjau Monografi RT</h1>
            <p class="mt-1 text-sm text-gray-500">Detail pengisian monografi RT tahun {{ $tahun }}.</p>
        </div>

        @if(($submission?->status ?? null) === 'submitted')
            <form method="POST" action="{{ route('admin.monografi.reopen', ['rtUser' => $rtUser, 'tahun' => $tahun]) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex h-[50px] items-center rounded-xl bg-amber-500 px-5 text-sm font-semibold text-white hover:bg-amber-600 transition">
                    Kembalikan ke Draft
                </button>
            </form>
        @endif
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <div class="rounded-2xl border border-gray-100 bg-white p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">RT</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-dark">{{ $rtUser->nama_lengkap }}</h2>
                    <p class="mt-2 text-sm text-gray-500">NIK: {{ $rtUser->nik ?: '-' }}</p>
                </div>
                <span class="inline-flex text-xs font-bold px-3 py-1 rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 px-4 py-4">
                    <dt class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Wilayah</dt>
                    <dd class="mt-2 text-sm font-semibold text-dark">{{ $wilayahLabel }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 px-4 py-4">
                    <dt class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Progress</dt>
                    <dd class="mt-2 text-sm font-semibold text-dark">{{ $filledCount }} Field Terisi</dd>
                </div>
                <div class="rounded-xl bg-slate-50 px-4 py-4">
                    <dt class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Terakhir Disimpan</dt>
                    <dd class="mt-2 text-sm font-semibold text-dark">{{ $submission?->last_saved_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 px-4 py-4">
                    <dt class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">Waktu Kirim</dt>
                    <dd class="mt-2 text-sm font-semibold text-dark">{{ $submission?->submitted_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6">
            <h2 class="text-lg font-extrabold text-dark">Ringkasan</h2>
            <div class="mt-5 space-y-4">
                <div class="rounded-xl border border-gray-100 px-4 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">Status Runtime</p>
                    <p class="mt-2 text-sm text-gray-600">Data lama tanpa submission tetap dibaca sebagai <span class="font-semibold text-dark">draft</span>.</p>
                </div>
                <div class="rounded-xl border border-gray-100 px-4 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">Tahun</p>
                    <p class="mt-2 text-sm font-semibold text-dark">{{ $tahun }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 px-4 py-4">
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">Jumlah Kategori</p>
                    <p class="mt-2 text-sm font-semibold text-dark">{{ $categories->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($categories as $category)
            <section class="rounded-2xl border border-gray-100 bg-white p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">
                        {{ $category->kode }}
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-dark">{{ $category->kode }}. {{ $category->nama_kategori }}</h2>
                        <p class="text-sm text-gray-500">{{ $category->fields->count() }} field</p>
                    </div>
                </div>

                @if($category->fields->isEmpty())
                    <div class="mt-6 rounded-xl border border-dashed border-gray-200 bg-slate-50 px-4 py-8 text-center text-sm font-semibold text-gray-500">
                        Kategori ini belum memiliki field.
                    </div>
                @else
                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        @foreach($category->fields as $field)
                            <div class="rounded-xl border border-gray-100 bg-slate-50 px-4 py-4">
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">{{ $field->label }}</p>
                                <p class="mt-2 text-sm font-semibold text-dark">{{ filled($fieldValues->get($field->id)) ? $fieldValues->get($field->id) : '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @empty
            <div class="rounded-2xl border border-gray-100 bg-white px-6 py-12 text-center text-sm font-semibold text-gray-500">
                Belum ada kategori monografi yang disiapkan.
            </div>
        @endforelse
    </div>
</div>
@endsection
