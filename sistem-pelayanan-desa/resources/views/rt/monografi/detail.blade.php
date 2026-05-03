@extends ("layouts.rt")
@section ("title", "Riwayat Monografi RT")

@php
    $fieldValues = collect($values ?? []);
    $statusLabel = match ($status) {
        "submitted" => "Sudah Dikirim",
        "draft" => "Draft",
        default => "Belum Isi",
    };
    $statusClass = match ($status) {
        "submitted" => "bg-green-50 text-green-600 border-green-100",
        "draft" => "bg-amber-50 text-amber-600 border-amber-100",
        default => "bg-slate-100 text-slate-600 border-slate-200",
    };
@endphp

@section ("content")
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <a
                    href="{{ route('rt.monografi.index', ['rt' => $selectedRt->id]) }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:text-primary-hover"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Kembali ke riwayat monografi
                </a>
                <h1 class="mt-3 text-[28px] font-extrabold leading-9 text-[#111827]">
                    Detail Monografi Tahun {{ $tahun }}
                </h1>
                <p class="mt-2 text-sm text-[#64748B]">
                    Riwayat pengisian monografi untuk {{ $wilayahLabel }} ditampilkan dalam mode baca.
                </p>
            </div>

            @if ($rtOptions->isNotEmpty())
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                        Pilih RT
                    </label>
                    <select
                        onchange="if (this.value) window.location.href = this.value"
                        class="h-[50px] min-w-[220px] rounded-[11px] border border-black/20 bg-white px-4 text-sm font-semibold text-[#111827] focus:border-primary focus:outline-none"
                    >
                        @foreach ($rtOptions as $rtOption)
                            <option
                                value="{{ route('rt.monografi.history', ['tahun' => $tahun, 'rt' => $rtOption->id]) }}"
                                {{ (int) $selectedRt?->id === (int) $rtOption->id ? "selected" : "" }}
                            >
                                {{ $rtOption->rt_label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-[24px] border border-gray-100 bg-white px-6 py-6 shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                            Wilayah
                        </p>
                        <h2 class="mt-2 text-2xl font-extrabold text-[#111827]">{{ $wilayahLabel }}</h2>
                        <p class="mt-1 text-sm text-[#64748B]">Tahun {{ $tahun }}</p>
                    </div>
                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[18px] bg-[#F8FAFC] px-4 py-4">
                        <dt class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                            Field Terisi
                        </dt>
                        <dd class="mt-2 text-sm font-semibold text-[#111827]">{{ $filledCount }}</dd>
                    </div>
                    <div class="rounded-[18px] bg-[#F8FAFC] px-4 py-4">
                        <dt class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                            Jumlah Kategori
                        </dt>
                        <dd class="mt-2 text-sm font-semibold text-[#111827]">{{ $categories->count() }}</dd>
                    </div>
                    <div class="rounded-[18px] bg-[#F8FAFC] px-4 py-4">
                        <dt class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                            Terakhir Disimpan
                        </dt>
                        <dd class="mt-2 text-sm font-semibold text-[#111827]">{{ $submission?->last_saved_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd>
                    </div>
                    <div class="rounded-[18px] bg-[#F8FAFC] px-4 py-4">
                        <dt class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                            Waktu Kirim
                        </dt>
                        <dd class="mt-2 text-sm font-semibold text-[#111827]">{{ $submission?->submitted_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-[24px] border border-gray-100 bg-white px-6 py-6 shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                    Aksi
                </p>
                <div class="mt-4 space-y-3">
                    @if ($tahun === now('Asia/Jakarta')->year && $status === 'draft')
                        <a
                            href="{{ route('rt.monografi.start', ['rt' => $selectedRt->id]) }}"
                            class="inline-flex h-[50px] items-center rounded-[12px] bg-primary px-5 text-sm font-semibold text-white transition hover:bg-primary-hover"
                        >
                            Lanjutkan Isi Tahun {{ $tahun }}
                        </a>
                    @endif

                    <p class="text-sm text-[#64748B]">
                        Riwayat ini ditampilkan dalam mode baca. Pengeditan hanya tersedia untuk draft tahun berjalan.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse ($categories as $category)
                <section class="rounded-[24px] border border-gray-100 bg-white px-6 py-6 shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">
                            {{ $category->kode }}
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-[#111827]">{{ $category->kode }}. {{ $category->nama_kategori }}</h2>
                            <p class="text-sm text-[#64748B]">{{ $category->fields->count() }} field</p>
                        </div>
                    </div>

                    @if ($category->fields->isEmpty())
                        <div class="mt-6 rounded-[18px] border border-dashed border-[#D7E3F8] bg-[#F8FAFC] px-6 py-8 text-center text-sm font-semibold text-[#64748B]">
                            Kategori ini belum memiliki field.
                        </div>
                    @else
                        <div class="mt-6 grid gap-4 lg:grid-cols-2">
                            @foreach ($category->fields as $field)
                                <div class="rounded-[18px] border border-gray-100 bg-[#F8FAFC] px-4 py-4">
                                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                                        {{ $field->label }}
                                    </p>
                                    <p class="mt-2 text-sm font-semibold text-[#111827]">
                                        {{ filled($fieldValues->get($field->id)) ? $fieldValues->get($field->id) : '-' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @empty
                <div class="rounded-[24px] border border-gray-100 bg-white px-6 py-12 text-center text-sm font-semibold text-[#64748B]">
                    Belum ada kategori monografi yang disiapkan.
                </div>
            @endforelse
        </div>
    </div>
@endsection
