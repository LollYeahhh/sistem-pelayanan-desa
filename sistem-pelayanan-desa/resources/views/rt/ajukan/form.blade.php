@extends ("layouts.rt")
@section ("title", "Lengkapi Dokumen RT - " . $layanan->nama_layanan)

@php
    $fieldStates = $fieldStates ?? [];
    $lockedFieldCount = collect($fieldStates)->where("is_locked", true)->count();
@endphp

@section ("content")
    <div class="space-y-8">
        {{-- Stepper --}}
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

        {{-- Header --}}
        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-[#0F172A]">
                Lengkapi Dokumen
            </h1>
            <p class="mt-2 text-sm text-[#64748B]">Isi formulir berikut untuk mengajukan
            <span class="font-semibold text-primary">{{ $layanan->nama_layanan }}</span>.</p>
        </div>

        {{-- Form --}}
        <form
            action="{{ route('rt.ajukan.store', $layanan) }}"
            method="POST"
            enctype="multipart/form-data"
            class="mx-auto max-w-3xl"
            id="ajukanForm"
        >
            @csrf

            @foreach ($layanan->sections as $sectionIndex => $section)
                <div
                    class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 mb-6"
                >
                    {{-- Section Header --}}
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <span
                                class="text-xs font-bold text-primary"
                                >{{ $loop->iteration }}</span
                            >
                        </div>
                        <h3 class="text-lg font-bold text-[#0F172A]">
                            {{
                                ucwords(
                                    $section->nama_section,
                                )
                            }}
                        </h3>
                    </div>

                    {{-- Fields --}}
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2 gap-x-5 gap-y-5"
                    >
                        @foreach ($section->fields as $field)
                            @php
                                $fieldName = "field_" . $field->id;
                                $isFullWidth = in_array($field->tipe_input, ["textarea", "file"]);
                                $fieldState = $fieldStates[$field->id] ?? [
                                    "is_identity" => false,
                                    "value" => null,
                                    "is_locked" => false,
                                ];
                                $prefillValue = old($fieldName, $fieldState["value"]);
                                $isLockedProfileField =
                                    $field->tipe_input !== "file" && $fieldState["is_locked"];
                            @endphp
                            <div
                                class="{{ $isFullWidth ? 'lg:col-span-2' : '' }}"
                            >
                                <label
                                    for="{{ $fieldName }}"
                                    class="mb-2 block text-sm font-semibold text-[#0F172A]"
                                >
                                    {{
                                        ucwords(
                                            $field->label,
                                        )
                                    }}
                                    @if ($field->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if ($isLockedProfileField)
                                    <div
                                        class="flex h-[48px] w-full cursor-not-allowed items-center rounded-xl border border-[#D7E3F8] bg-[#F8FBFF] px-4 text-sm text-[#0F172A] opacity-70"
                                    >
                                        {{ $prefillValue }}
                                    </div>
                                    <input
                                        type="hidden"
                                        id="{{ $fieldName }}"
                                        name="{{ $fieldName }}"
                                        value="{{ $prefillValue }}"
                                    />
                                @else
                                    @switch ($field->tipe_input)
                                        @case ('text')
                                            <input
                                                id="{{ $fieldName }}"
                                                name="{{ $fieldName }}"
                                                type="text"
                                                value="{{ $prefillValue }}"
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
                                                value="{{ $prefillValue }}"
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
                                                value="{{ $prefillValue }}"
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
                                                            $prefillValue === $opsi
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
                                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-4 py-3 text-sm text-[#0f172a] placeholder-[#94a3b8] focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none"
                                                {{
                                                    $field->is_required
                                                        ? "required"
                                                        : ""
                                                }}
                                                >{{ $prefillValue }}</textarea
                                            >
                                            @break
                                        @case ('file')
                                            <div class="relative">
                                                <label
                                                    for="{{ $fieldName }}"
                                                    id="upload_box_{{ $field->id }}"
                                                    class="flex flex-col items-center justify-center w-full h-32 bg-[#f8fafc] border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition group"
                                                >
                                                    <svg
                                                        id="upload_icon_{{ $field->id }}"
                                                        class="w-8 h-8 text-[#94a3b8] group-hover:text-primary transition mb-2"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="1.5"
                                                    >
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                    </svg>
                                                    <span
                                                        id="upload_title_{{ $field->id }}"
                                                        class="text-sm text-[#94a3b8] group-hover:text-primary font-medium transition"
                                                        >Klik untuk unggah {{ $field->label }}</span
                                                    >
                                                    <span
                                                        id="upload_subtitle_{{ $field->id }}"
                                                        class="text-xs text-[#cbd5e1] mt-1"
                                                        >Maks. 2MB (JPG, PNG,
                                                        PDF)</span
                                                    >
                                                </label>
                                                <input
                                                    type="file"
                                                    id="{{ $fieldName }}"
                                                    name="{{ $fieldName }}"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    class="hidden"
                                                    {{
                                                        $field->is_required
                                                            ? "required"
                                                            : ""
                                                    }}
                                                    onchange="
                                                        updateFileLabel(this)
                                                    "
                                                />
                                            </div>
                                            @break
                                    @endswitch
                                @endif

                                @error ($fieldName)
                                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Declaration Checkbox --}}
            <div
                class="bg-[#E1EFFF] border border-[#3B82F6]/30 rounded-2xl px-5 py-4 mt-6 mb-4"
            >
                <label class="flex items-start gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        id="declarationCheckbox"
                        class="w-[18px] h-[18px] rounded border-gray-300 text-[#1D4ED8] focus:ring-[#1D4ED8] mt-0.5 shrink-0"
                        required
                    />
                    <span class="text-xs text-[#1D4ED8]">
                        Saya menyatakan bahwa data yang saya isikan di atas
                        adalah
                        <strong class="underline text-[#1D4ED8]"
                            >benar dan dapat dipertanggungjawabkan secara
                            hukum</strong
                        >.
                    </span>
                </label>
            </div>

            {{-- Buttons --}}
            <div class="mt-4 flex items-center justify-between">
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
                    type="button"
                    id="btnLanjutkan"
                    onclick="showConfirmModal()"
                    class="inline-flex items-center gap-2 bg-primary text-white font-bold text-sm px-8 py-3 rounded-xl transition shadow-md shadow-primary/30 opacity-50 cursor-not-allowed"
                    disabled
                >
                    Lanjutkan
                </button>
            </div>
        </form>
    </div>
    {{-- Confirmation Modal --}}
    <div
        id="confirmModal"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40 backdrop-blur-sm"
            onclick="hideConfirmModal()"
        ></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center animate-fade-in-up"
        >
            <div
                class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4"
            >
                <svg class="w-8 h-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#0f172a] mb-2">
                Konfirmasi Pengajuan
            </h3>
            <p class="text-sm text-[#64748b] mb-6">Data yang telah dikirim <strong>tidak dapat diubah</strong>. Pastikan semua informasi yang Anda masukkan sudah benar.</p>
            <div class="flex gap-3">
                <button
                    type="button"
                    onclick="hideConfirmModal()"
                    class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-[#64748b] hover:bg-gray-50 transition"
                >
                    Periksa Lagi
                </button>
                <button
                    type="button"
                    onclick="submitForm()"
                    class="flex-1 px-4 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-hover transition shadow-md shadow-primary/30"
                >
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>
    {{-- Success Modal --}}
    <div
        id="successModal"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4"
    >
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div
            class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center animate-fade-in-up"
        >
            <div
                class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4"
            >
                <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#0f172a] mb-2">
                Dokumen Berhasil Dikirim!
            </h3>
            <p class="text-sm text-[#64748b] mb-6">Permohonan surat Anda telah berhasil diajukan. Silakan pantau status permohonan di halaman Riwayat.</p>
            <a
                href="{{ route('rt.riwayat.index') }}"
                class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-hover transition shadow-md shadow-primary/30"
            >
                Lihat Riwayat
            </a>
        </div>
    </div>
    <script>
        function updateFileLabel(input) {
            const fieldId = input.id.replace("field_", "");
            const uploadBox = document.getElementById("upload_box_" + fieldId);
            const uploadIcon = document.getElementById(
                "upload_icon_" + fieldId,
            );
            const uploadTitle = document.getElementById(
                "upload_title_" + fieldId,
            );
            const uploadSubtitle = document.getElementById(
                "upload_subtitle_" + fieldId,
            );

            if (!uploadBox || !uploadIcon || !uploadTitle || !uploadSubtitle)
                return;

            if (input.files && input.files[0]) {
                uploadBox.classList.remove(
                    "border-dashed",
                    "border-gray-200",
                    "bg-[#f8fafc]",
                );
                uploadBox.classList.add(
                    "border-solid",
                    "border-emerald-300",
                    "bg-emerald-50",
                );

                uploadIcon.classList.remove(
                    "text-[#94a3b8]",
                    "group-hover:text-primary",
                );
                uploadIcon.classList.add("text-emerald-500");
                uploadIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />';

                uploadTitle.classList.remove(
                    "text-[#94a3b8]",
                    "group-hover:text-primary",
                );
                uploadTitle.classList.add(
                    "text-emerald-700",
                    "font-semibold",
                    "max-w-[92%]",
                    "truncate",
                );
                uploadTitle.textContent = input.files[0].name;

                uploadSubtitle.classList.remove("text-[#cbd5e1]");
                uploadSubtitle.classList.add("text-emerald-600");
                uploadSubtitle.textContent =
                    "File berhasil dipilih • Klik untuk ganti";
            }
        }

        function checkFormCompleteness() {
            const form = document.getElementById("ajukanForm");
            const btn = document.getElementById("btnLanjutkan");
            const requiredFields = form.querySelectorAll("[required]");
            let allFilled = true;

            requiredFields.forEach(function (field) {
                if (field.type === "checkbox") {
                    if (!field.checked) allFilled = false;
                } else if (field.type === "file") {
                    if (!field.files || field.files.length === 0)
                        allFilled = false;
                } else {
                    if (!field.value || field.value.trim() === "")
                        allFilled = false;
                }
            });

            if (allFilled) {
                btn.disabled = false;
                btn.classList.remove("opacity-50", "cursor-not-allowed");
                btn.classList.add("hover:bg-primary-hover");
            } else {
                btn.disabled = true;
                btn.classList.add("opacity-50", "cursor-not-allowed");
                btn.classList.remove("hover:bg-primary-hover");
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("ajukanForm");
            form.addEventListener("input", checkFormCompleteness);
            form.addEventListener("change", checkFormCompleteness);
            checkFormCompleteness();
        });

        function showConfirmModal() {
            const btn = document.getElementById("btnLanjutkan");
            if (btn.disabled) return;

            const form = document.getElementById("ajukanForm");
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            document.getElementById("confirmModal").classList.remove("hidden");
        }

        function hideConfirmModal() {
            document.getElementById("confirmModal").classList.add("hidden");
        }

        function submitForm() {
            hideConfirmModal();
            document.getElementById("ajukanForm").submit();
        }
    </script>
@endsection
