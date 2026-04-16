@extends ("layouts.rt")
@section ("title", "Riwayat Monografi RT")

@php
    $currentStatus = $currentEntry->status ?? "kosong";
    $statusLabel = match ($currentStatus) {
        "submitted" => "Sudah Dikirim",
        "draft" => ($currentEntry && $currentEntry->has_data) ? "Draft" : "Belum Isi",
        default => "Belum Isi",
    };
    $statusClass = match ($currentStatus) {
        "submitted" => "border-green-100 bg-green-50 text-green-600",
        "draft" => ($currentEntry && $currentEntry->has_data)
            ? "border-amber-100 bg-amber-50 text-amber-600"
            : "border-slate-200 bg-slate-100 text-slate-600",
        default => "border-slate-200 bg-slate-100 text-slate-600",
    };
@endphp

@section ("content")
    <div class="space-y-5">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                    Monografi RT
                </p>
                <h1 class="mt-2 text-[28px] font-extrabold leading-tight text-[#111827] lg:text-[32px]">
                    Riwayat Pengisian Monografi
                </h1>
                <p class="mt-2 text-sm text-[#64748B] lg:text-[15px]">
                    Pantau status monografi per tahun, lalu lanjutkan draft atau mulai pengisian tahun {{ $tahunAktif }} dari sini.
                </p>
            </div>

            @if ($rtOptions->isNotEmpty())
                <div class="w-full xl:w-auto">
                    <label class="mb-2 block text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                        Pilih RT
                    </label>
                    <select
                        onchange="if (this.value) window.location.href = this.value"
                        class="h-[54px] w-full rounded-[14px] border border-black/15 bg-white px-4 text-sm font-semibold text-[#111827] shadow-[0_10px_24px_-24px_rgba(17,24,39,0.5)] transition focus:border-primary focus:outline-none xl:min-w-[300px]"
                    >
                        @foreach ($rtOptions as $rtOption)
                            <option
                                value="{{ route('rt.monografi.index', ['rt' => $rtOption->id]) }}"
                                {{ (int) $selectedRt?->id === (int) $rtOption->id ? "selected" : "" }}
                            >
                                {{ $rtOption->rt_label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <section class="overflow-hidden rounded-[28px] border border-gray-100 bg-white shadow-[0_20px_40px_-32px_rgba(17,24,39,0.45)]">
            <div class="grid gap-0 lg:grid-cols-[1.25fr_0.75fr]">
                <div class="px-6 py-6 sm:px-7 lg:px-8 lg:py-7">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                                Tahun Aktif
                            </p>
                            <h2 class="mt-2 text-[26px] font-extrabold leading-tight text-[#111827]">
                                Monografi {{ $tahunAktif }}
                            </h2>
                            <p class="mt-2 text-sm text-[#64748B]">
                                Wilayah {{ $wilayahLabel }}.
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-6 flex flex-col gap-4 lg:flex-row lg:items-center">
                        @if (($primaryAction["kind"] ?? "start") === "view")
                            <a
                                href="{{ route('rt.monografi.history', ['tahun' => $tahunAktif, 'rt' => $selectedRt->id]) }}"
                                class="inline-flex min-h-[54px] w-full items-center justify-center rounded-[16px] bg-slate-900 px-6 text-center text-sm font-semibold text-white transition hover:bg-slate-800 lg:w-auto"
                            >
                                {{ $primaryAction["label"] }} Tahun {{ $tahunAktif }}
                            </a>
                        @else
                            <a
                                href="{{ route('rt.monografi.start', ['rt' => $selectedRt->id]) }}"
                                class="inline-flex min-h-[54px] w-full items-center justify-center rounded-[16px] bg-primary px-6 text-center text-sm font-semibold text-white shadow-[0_18px_30px_-20px_rgba(37,99,235,0.9)] transition hover:bg-primary-hover lg:w-auto"
                            >
                                {{ $primaryAction["label"] }} Tahun {{ $tahunAktif }}
                            </a>
                        @endif

                        <p class="max-w-xl text-sm leading-6 text-[#64748B]">
                            @if ($currentStatus === "submitted")
                                Data tahun aktif sudah dikirim dan hanya bisa dibuka dalam mode lihat.
                            @elseif ($currentEntry && $currentEntry->has_data)
                                Draft tahun aktif sudah tersedia dan bisa dilanjutkan kapan saja.
                            @else
                                Belum ada data tahun aktif. Mulai pengisian dari kategori pertama.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-[#F8FAFC] px-6 py-6 sm:px-7 lg:border-l lg:border-t-0 lg:px-8 lg:py-7">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                        Ringkasan
                    </p>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="rounded-[18px] border border-white/70 bg-white px-4 py-4 shadow-[0_10px_24px_-28px_rgba(15,23,42,0.5)]">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                                Wilayah Aktif
                            </p>
                            <p class="mt-2 text-base font-extrabold text-[#111827]">{{ $wilayahLabel }}</p>
                        </div>
                        <div class="rounded-[18px] border border-white/70 bg-white px-4 py-4 shadow-[0_10px_24px_-28px_rgba(15,23,42,0.5)]">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                                Kategori
                            </p>
                            <p class="mt-2 text-base font-extrabold text-[#111827]">{{ $categories->count() }} Kategori</p>
                        </div>
                        <div class="rounded-[18px] border border-white/70 bg-white px-4 py-4 shadow-[0_10px_24px_-28px_rgba(15,23,42,0.5)]">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                                Riwayat
                            </p>
                            <p class="mt-2 text-base font-extrabold text-[#111827]">{{ $historyEntries->count() }} Tahun</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-gray-100 bg-white px-4 py-5 shadow-[0_20px_40px_-32px_rgba(17,24,39,0.45)] sm:px-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-[#111827]">Riwayat Pengisian</h2>
                    <p class="mt-1 text-sm text-[#64748B]">
                        Setiap tahun ditampilkan dengan status dan aksi yang sesuai.
                    </p>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                        <tr class="text-left text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                            <th class="px-3 py-3">Tahun</th>
                            <th class="px-3 py-3">Wilayah</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Terakhir Disimpan</th>
                            <th class="px-3 py-3">Waktu Kirim</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($historyEntries as $entry)
                            @php
                                $entryStatusLabel = match ($entry->status) {
                                    "submitted" => "Sudah Dikirim",
                                    "draft" => $entry->has_data ? "Draft" : "Belum Isi",
                                    default => "Belum Isi",
                                };
                                $entryStatusClass = match ($entry->status) {
                                    "submitted" => "border-green-100 bg-green-50 text-green-600",
                                    "draft" => $entry->has_data
                                        ? "border-amber-100 bg-amber-50 text-amber-600"
                                        : "border-slate-200 bg-slate-100 text-slate-600",
                                    default => "border-slate-200 bg-slate-100 text-slate-600",
                                };
                            @endphp
                            <tr class="align-top">
                                <td class="px-3 py-4 text-sm font-extrabold text-[#111827]">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $entry->tahun }}</span>
                                        @if ($entry->is_current_year)
                                            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.08em] text-primary">
                                                Aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm font-semibold text-[#475569]">
                                    {{ $entry->wilayah_label }}
                                </td>
                                <td class="px-3 py-4">
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $entryStatusClass }}">
                                        {{ $entryStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-sm text-[#475569]">
                                    {{ $entry->last_saved_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </td>
                                <td class="px-3 py-4 text-sm text-[#475569]">
                                    {{ $entry->submitted_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </td>
                                <td class="px-3 py-4 text-right">
                                    @if ($entry->is_current_year && $entry->status !== 'submitted')
                                        <a
                                            href="{{ route('rt.monografi.start', ['rt' => $selectedRt->id]) }}"
                                            class="inline-flex h-10 items-center rounded-[11px] {{ $entry->has_data ? 'border border-[#D7E3F8] bg-white px-4 text-primary hover:border-primary/30 hover:bg-[#F8FBFF]' : 'bg-primary px-4 text-white hover:bg-primary-hover' }} text-sm font-semibold transition"
                                        >
                                            {{ $entry->has_data ? 'Lanjutkan Isi' : 'Isi Data Monografi' }}
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('rt.monografi.history', ['tahun' => $entry->tahun, 'rt' => $selectedRt->id]) }}"
                                            class="inline-flex h-10 items-center rounded-[11px] border border-[#D7E3F8] bg-white px-4 text-sm font-semibold text-[#475569] transition hover:border-primary/30 hover:text-primary"
                                        >
                                            Lihat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-12 text-center text-sm font-semibold text-[#64748B]">
                                    Belum ada riwayat monografi untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
