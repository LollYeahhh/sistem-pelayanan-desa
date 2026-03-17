@extends ("layouts.rt")
@section ("title", "Lengkapi Dokumen RT - " . $layanan->nama_layanan)

@section ("content")
    <div class="space-y-8">
        <div class="flex items-center justify-center gap-0">
            <div class="flex items-center gap-2">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <span
                    class="hidden text-sm font-semibold text-primary sm:inline"
                    >Pilih Layanan</span
                >
            </div>
            <div class="mx-2 h-[2px] w-12 bg-primary lg:w-20"></div>
            <div class="flex items-center gap-2">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white shadow-md shadow-primary/30"
                >
                    2
                </div>
                <span
                    class="hidden text-sm font-semibold text-primary sm:inline"
                    >Lengkapi Dokumen</span
                >
            </div>
            <div class="mx-2 h-[2px] w-12 bg-gray-200 lg:w-20"></div>
            <div class="flex items-center gap-2">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-[#94A3B8]"
                >
                    3
                </div>
                <span
                    class="hidden text-sm font-medium text-[#94A3B8] sm:inline"
                    >Konfirmasi</span
                >
            </div>
        </div>

        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-[#0F172A]">
                Lengkapi Dokumen
            </h1>
            <p class="mt-2 text-sm text-[#64748B]">Isi formulir berikut untuk mengajukan <span class="font-semibold text-primary">{{ $layanan->nama_layanan }}</span>.</p>
        </div>

        <form
            action="{{ route('rt.ajukan.store', $layanan) }}"
            method="POST"
            enctype="multipart/form-data"
            class="mx-auto max-w-3xl"
        >
            @csrf

            @foreach ($layanan->sections as $sectionIndex => $section)
                <div
                    class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 lg:p-8"
                >
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <span
                                class="text-xs font-bold text-primary"
                                >{{ $sectionIndex + 1 }}</span
                            >
                        </div>
                        <h3 class="text-lg font-bold text-[#0F172A]">
                            {{ $section->nama_section }}
                        </h3>
                    </div>

                    <div class="space-y-5">
                        @foreach ($section->fields as $field)
                            @php
                                $fieldName = "field_" . $field->id;
                            @endphp
                            <div>
                                <label
                                    for="{{ $fieldName }}"
                                    class="mb-2 block text-sm font-semibold text-[#0F172A]"
                                >
                                    {{ $field->label }}
                                    @if ($field->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @switch ($field->tipe_input)
                                    @case ('text')
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="text"
                                            value="{{ old($fieldName) }}"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan ' . strtolower($field->label) }}"
                                            class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                            {{
                                                $field->is_required
                                                    ? "required"
                                                    : ""
                                            }}
                                        />
                                        @break
                                    @case ('number')
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="number"
                                            value="{{ old($fieldName) }}"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan angka' }}"
                                            class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] placeholder-[#94A3B8] transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                            {{
                                                $field->is_required
                                                    ? "required"
                                                    : ""
                                            }}
                                        />
                                        @break
                                    @case ('date')
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="date"
                                            value="{{ old($fieldName) }}"
                                            class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                            {{
                                                $field->is_required
                                                    ? "required"
                                                    : ""
                                            }}
                                        />
                                        @break
                                    @case ('select')
                                        <select
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                            {{
                                                $field->is_required
                                                    ? "required"
                                                    : ""
                                            }}
                                        >
                                            <option value="">
                                                -- Pilih {{ $field->label }} --
                                            </option>
                                            @foreach ($field->opsi_select ?? [] as $opsi)
                                                <option
                                                    value="{{ $opsi }}"
                                                    {{
                                                        old($fieldName) === $opsi
                                                                ? "selected"
                                                                : ""
                                                    }}
                                                >
                                                    {{ $opsi }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case ('textarea')
                                        <textarea
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            rows="4"
                                            placeholder="{{ $field->placeholder ?? 'Tuliskan ' . strtolower($field->label) }}"
                                            class="w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 py-3 text-sm text-[#0F172A] placeholder-[#94A3B8] transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                                            {{
                                                $field->is_required
                                                    ? "required"
                                                    : ""
                                            }}
                                            >{{
                                                old(
                                                    $fieldName,
                                                )
                                            }}</textarea
                                        >
                                        @break
                                    @case ('file')
                                        <input
                                            id="{{ $fieldName }}"
                                            name="{{ $fieldName }}"
                                            type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            class="block w-full rounded-xl border border-dashed border-gray-200 bg-[#F8FAFC] px-4 py-3 text-sm text-[#64748B] transition file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:font-semibold file:text-primary hover:border-primary/40"
                                            {{
                                                $field->is_required
                                                    ? "required"
                                                    : ""
                                            }}
                                        />
                                        @break
                                @endswitch

                                @error ($fieldName)
                                    <p class="mt-2 text-xs font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="mt-6 flex items-center justify-between">
                <a
                    href="{{ route('rt.ajukan.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748B] transition hover:text-[#0F172A]"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3 text-sm font-bold text-white shadow-md shadow-primary/30 transition hover:bg-primary-hover"
                >
                    Kirim Permohonan
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
@endsection
