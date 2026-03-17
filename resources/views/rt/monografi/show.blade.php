@extends ("layouts.rt")
@section ("title", "Data Monografi RT")

@php
    $fieldValues = collect($values ?? []);
@endphp

@section ("content")
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-[28px] font-extrabold leading-9 text-[#111827]">
                Data Monografi RT
            </h1>

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
                                {{ $selectedRt->rt_label }} - Tahun {{ $tahun }}
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
                                            class="w-full rounded-[14px] bg-[#F8FAFC] px-5 py-4 text-sm text-[#111827] placeholder-[#B3BDC9] outline-none ring-1 ring-transparent transition focus:ring-2 focus:ring-primary/20"
                                        >{{ $fieldValue }}</textarea>
                                    @elseif ($field->tipe_input === 'date')
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="date"
                                            value="{{ $fieldValue }}"
                                            class="h-[57px] w-full rounded-[14px] bg-[#F8FAFC] px-5 text-sm text-[#111827] placeholder-[#B3BDC9] outline-none ring-1 ring-transparent transition focus:ring-2 focus:ring-primary/20"
                                        />
                                    @else
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="{{ $field->tipe_input === 'number' ? 'number' : 'text' }}"
                                            value="{{ $fieldValue }}"
                                            placeholder="Masukkan {{ strtolower($field->tipe_input === 'number' ? 'jumlah' : $field->label) }}"
                                            class="h-[57px] w-full rounded-[14px] bg-[#F8FAFC] px-5 text-sm text-[#111827] placeholder-[#B3BDC9] outline-none ring-1 ring-transparent transition focus:ring-2 focus:ring-primary/20"
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
                            <div class="flex gap-3">
                                @if ($previousCategory)
                                    <button
                                        type="submit"
                                        name="action"
                                        value="previous"
                                        class="inline-flex h-[50px] items-center rounded-[11px] border border-[#D7E3F8] bg-white px-5 text-sm font-semibold text-[#64748B] transition hover:border-primary/30 hover:text-primary"
                                    >
                                        Sebelumnya
                                    </button>
                                @endif

                                <button
                                    type="submit"
                                    name="action"
                                    value="save"
                                    class="inline-flex h-[50px] items-center rounded-[11px] border border-[#D7E3F8] bg-white px-5 text-sm font-semibold text-primary transition hover:border-primary/30 hover:bg-[#F8FBFF]"
                                >
                                    Simpan Draft
                                </button>
                            </div>

                            <button
                                type="submit"
                                name="action"
                                value="next"
                                class="inline-flex h-[50px] items-center rounded-[11px] bg-primary px-6 text-sm font-semibold text-white shadow-[0_12px_30px_-18px_rgba(37,99,235,0.9)] transition hover:bg-primary-hover"
                            >
                                {{ $nextCategory ? 'Selanjutnya' : 'Simpan' }}
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        @endif
    </div>
@endsection



