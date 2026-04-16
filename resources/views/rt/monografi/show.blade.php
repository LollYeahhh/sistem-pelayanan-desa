@extends ("layouts.rt")
@section ("title", "Data Monografi RT")

@php
    $fieldValues = collect($values ?? []);
    $submissionStatus = $submission?->status ?? "draft";
    $statusLabel = $submissionStatus === "submitted" ? "Sudah Dikirim" : "Draft";
    $statusClass = $submissionStatus === "submitted"
        ? "bg-green-50 text-green-600 border-green-100"
        : "bg-amber-50 text-amber-600 border-amber-100";
@endphp

@section ("content")
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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
                    Isi Data Monografi RT
                </h1>
                <p class="mt-2 text-sm text-[#64748B]">
                    Lengkapi data monografi tahun {{ $tahun }} secara bertahap per kategori.
                </p>
            </div>

            @if ($rtOptions->isNotEmpty())
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">
                        Pilih RT
                    </label>
                    <select
                        onchange="if (this.value) window.location.href = this.value"
                        class="h-[50px] rounded-[11px] border border-black/20 bg-white px-4 text-sm font-semibold text-[#111827] focus:border-primary focus:outline-none"
                    >
                        @foreach ($rtOptions as $rtOption)
                            <option
                                value="{{ route($currentCategory ? 'rt.monografi.show' : 'rt.monografi.index', $currentCategory ? ['category' => $currentCategory, 'rt' => $rtOption->id] : ['rt' => $rtOption->id]) }}"
                                {{ (int) $selectedRt?->id === (int) $rtOption->id ? "selected" : "" }}
                            >
                                {{ $rtOption->rt_label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-[24px] border border-gray-100 bg-white px-6 py-5 shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">Wilayah Aktif</p>
                        <h2 class="mt-2 text-xl font-extrabold text-[#111827]">{{ $wilayahLabel ?? ($selectedRt->rt_label ?? '-') }}</h2>
                        <p class="mt-1 text-sm text-[#64748B]">Pengisian untuk tahun {{ $tahun }}</p>
                    </div>
                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="rounded-[24px] border border-gray-100 bg-white px-6 py-5 shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]">Status Pengisian</p>
                <p class="mt-2 text-sm text-[#475569]">
                    @if ($submissionStatus === "submitted")
                        Data monografi sudah dikirim. Form dikunci sampai admin mengembalikannya ke draft.
                    @else
                        Data masih dalam status draft. Anda bisa menyimpan bertahap lalu kirim final setelah semua field terisi.
                    @endif
                </p>
                @error ("submit")
                    <p class="mt-3 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        @if (! $currentCategory)
            <div class="rounded-[24px] bg-white px-6 py-12 text-center shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)]">
                <p class="text-sm font-semibold text-[#64748B]">
                    Kategori monografi belum tersedia.
                </p>
            </div>
        @else
            <div class="flex gap-3 overflow-x-auto pb-1">
                @foreach ($categories as $category)
                    <a
                        href="{{ route('rt.monografi.show', ['category' => $category, 'rt' => $selectedRt->id]) }}"
                        class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition {{ (int) $currentCategory->id === (int) $category->id ? 'bg-primary text-white' : 'border border-[#D7E3F8] bg-white text-[#64748B] hover:border-primary/30 hover:text-primary' }}"
                    >
                        {{ $category->kode }}. {{ $category->nama_kategori }}
                    </a>
                @endforeach
            </div>

            <div class="rounded-[24px] bg-white px-4 py-6 shadow-[0_18px_34px_-30px_rgba(17,24,39,0.45)] sm:px-6 lg:px-[50px] lg:py-8">
                <form
                    action="{{ route('rt.monografi.update', ['category' => $currentCategory, 'rt' => $selectedRt->id]) }}"
                    method="POST"
                    class="space-y-8"
                >
                    @csrf
                    @method ("PUT")

                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-sm font-extrabold text-primary">
                            {{ $currentCategory->kode }}
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-[#111827]">
                                {{ $currentCategory->kode }}. {{ $currentCategory->nama_kategori }}
                            </h2>
                            <p class="text-sm text-[#64748B]">
                                {{ $wilayahLabel ?? $selectedRt->rt_label }} - Tahun {{ $tahun }}
                            </p>
                        </div>
                    </div>

                    @if ($fields->isEmpty())
                        <div class="rounded-[18px] border border-dashed border-[#D7E3F8] bg-[#F8FAFC] px-6 py-12 text-center">
                            <p class="text-sm font-semibold text-[#64748B]">
                                Field monografi untuk kategori ini belum disiapkan admin.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                @if ($previousCategory)
                                    <a
                                        href="{{ route('rt.monografi.show', ['category' => $previousCategory, 'rt' => $selectedRt->id]) }}"
                                        class="inline-flex h-[50px] items-center rounded-[11px] border border-[#D7E3F8] bg-white px-5 text-sm font-semibold text-[#64748B] transition hover:border-primary/30 hover:text-primary"
                                    >
                                        Sebelumnya
                                    </a>
                                @endif
                            </div>

                            @if ($nextCategory)
                                <a
                                    href="{{ route('rt.monografi.show', ['category' => $nextCategory, 'rt' => $selectedRt->id]) }}"
                                    class="inline-flex h-[50px] items-center rounded-[11px] bg-primary px-6 text-sm font-semibold text-white shadow-[0_12px_30px_-18px_rgba(37,99,235,0.9)] transition hover:bg-primary-hover"
                                >
                                    Selanjutnya
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="grid gap-x-[80px] gap-y-8 lg:grid-cols-2">
                            @foreach ($fields as $field)
                                @php
                                    $fieldName = 'field_' . $field->id;
                                    $fieldValue = old($fieldName, $fieldValues->get($field->id));
                                @endphp
                                <div>
                                    <label
                                        for="{{ $fieldName }}"
                                        class="mb-3 block text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#A3AEC2]"
                                    >
                                        {{ $field->label }}
                                    </label>

                                    @if ($field->tipe_input === 'textarea')
                                        <textarea
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            rows="4"
                                            placeholder="Masukkan {{ strtolower($field->label) }}"
                                            @disabled($isReadOnly)
                                            class="w-full rounded-[14px] bg-[#F8FAFC] px-5 py-4 text-sm text-[#111827] placeholder-[#B3BDC9] outline-none ring-1 ring-transparent transition focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                        >{{ $fieldValue }}</textarea>
                                    @elseif ($field->tipe_input === 'date')
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="date"
                                            value="{{ $fieldValue }}"
                                            @disabled($isReadOnly)
                                            class="h-[57px] w-full rounded-[14px] bg-[#F8FAFC] px-5 text-sm text-[#111827] placeholder-[#B3BDC9] outline-none ring-1 ring-transparent transition focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                        />
                                    @else
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="{{ $field->tipe_input === 'number' ? 'number' : 'text' }}"
                                            value="{{ $fieldValue }}"
                                            placeholder="Masukkan {{ strtolower($field->tipe_input === 'number' ? 'jumlah' : $field->label) }}"
                                            @disabled($isReadOnly)
                                            class="h-[57px] w-full rounded-[14px] bg-[#F8FAFC] px-5 text-sm text-[#111827] placeholder-[#B3BDC9] outline-none ring-1 ring-transparent transition focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                                        />
                                    @endif

                                    @error ($fieldName)
                                        <p class="mt-2 text-xs font-medium text-red-500">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-wrap gap-3">
                                @if ($previousCategory)
                                    <a
                                        href="{{ route('rt.monografi.show', ['category' => $previousCategory, 'rt' => $selectedRt->id]) }}"
                                        class="inline-flex h-[50px] items-center rounded-[11px] border border-[#D7E3F8] bg-white px-5 text-sm font-semibold text-[#64748B] transition hover:border-primary/30 hover:text-primary"
                                    >
                                        Sebelumnya
                                    </a>
                                @endif

                                @unless ($isReadOnly)
                                    <button
                                        type="submit"
                                        name="action"
                                        value="save"
                                        class="inline-flex h-[50px] items-center rounded-[11px] border border-[#D7E3F8] bg-white px-5 text-sm font-semibold text-primary transition hover:border-primary/30 hover:bg-[#F8FBFF]"
                                    >
                                        Simpan Draft
                                    </button>
                                @endunless
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @if ($nextCategory)
                                    <button
                                        type="submit"
                                        name="action"
                                        value="save_and_next"
                                        @disabled($isReadOnly)
                                        class="inline-flex h-[50px] items-center rounded-[11px] bg-primary px-6 text-sm font-semibold text-white shadow-[0_12px_30px_-18px_rgba(37,99,235,0.9)] transition hover:bg-primary-hover disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none"
                                    >
                                        Simpan &amp; Lanjutkan
                                    </button>
                                @elseif (! $isReadOnly)
                                    <button
                                        type="submit"
                                        name="action"
                                        value="submit"
                                        class="inline-flex h-[50px] items-center rounded-[11px] bg-emerald-600 px-6 text-sm font-semibold text-white shadow-[0_12px_30px_-18px_rgba(5,150,105,0.9)] transition hover:bg-emerald-700"
                                    >
                                        Kirim Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        @endif
    </div>
@endsection
