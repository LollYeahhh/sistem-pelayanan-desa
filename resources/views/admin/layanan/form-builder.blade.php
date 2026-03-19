@extends ("layouts.admin")

@section ("title", "Form Builder: " . $layanan->nama_layanan)

@section ("content")
    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3">
            <a
                href="{{ route('admin.layanan.index') }}"
                class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-500 transition hover:bg-gray-200"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-dark lg:text-[24px]">
                    Form Builder: {{ $layanan->nama_layanan }}
                </h1>
                <p class="mt-1 text-sm text-gray">Rancang struktur form yang akan tampil di aplikasi warga.</p>

            </div>
        </div>
        <div class="flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center">
            <button
                onclick="openModal('sectionModal')"
                class="flex h-[50px] items-center justify-center gap-2 rounded-xl bg-[#0F172A] px-5 text-sm font-semibold text-white transition hover:bg-[#1E293B]"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambahkan Section
            </button>
            <form method="POST" action="{{ route('admin.layanan.publish', $layanan) }}">
                @csrf
                @method ("PATCH")
                <button
                    type="submit"
                    class="flex h-[50px] items-center justify-center gap-2 rounded-xl bg-primary px-5 text-sm font-semibold text-white transition hover:bg-primary-hover"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Publikasikan Form
                </button>
            </form>
        </div>
    </div>
    {{-- Flash --}}
    @if (session("success"))
        <div
            id="flash-msg"
            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center justify-between"
        >
            <span>{{
                session(
                    "success",
                )
            }}</span>
            <button
                onclick="this.parentElement.remove()"
                class="text-green-500"
            >
                &times;
            </button>
        </div>
    @endif
    {{-- Sections --}}
    @forelse ($layanan->sections as $section)
        <div
            class="bg-white rounded-2xl border border-gray-light mb-6 overflow-hidden"
        >
            {{-- Section Header --}}
            <div
                class="flex items-center justify-between px-6 py-4 bg-gray-50/50 border-b border-gray-100"
            >
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <h3 class="text-sm font-bold text-dark">
                        Section {{ $section->urutan }}: {{ ucwords($section->nama_section) }}
                    </h3>
                </div>
                <form
                    method="POST"
                    action="{{ route('admin.layanan.sections.destroy', [$layanan, $section]) }}"
                    onsubmit="
                        return confirm(
                            'Hapus section ini beserta semua field-nya?',
                        );
                    "
                >
                    @csrf
                    @method ("DELETE")
                    <button
                        type="submit"
                        class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-400 hover:bg-red-100 transition"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Fields Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th
                                class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[60px]"
                            >
                                No
                            </th>
                            <th
                                class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3"
                            >
                                Label Field
                            </th>
                            <th
                                class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[140px]"
                            >
                                Tipe Input
                            </th>
                            <th
                                class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[80px]"
                            >
                                Wajib
                            </th>
                            <th
                                class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[100px]"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($section->fields as $index => $field)
                            <tr
                                class="border-b border-gray-50 hover:bg-gray-50/50"
                            >
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm font-medium text-dark"
                                >
                                    {{
                                        ucwords(
                                            $field->label,
                                        )
                                    }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-block text-[11px] font-bold px-3 py-1 rounded-full {{ $field->tipe_badge_color }}"
                                    >
                                        {{ $field->tipe_label }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 text-center text-sm font-semibold {{ $field->is_required ? 'text-green-600' : 'text-gray-400' }}"
                                >
                                    {{
                                        $field->is_required
                                            ? "YA"
                                            : "TIDAK"
                                    }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="flex items-center justify-center gap-1.5"
                                    >
                                        <button
                                            onclick="openEditFieldModal({{ $field->id }}, '{{ addslashes($field->label) }}', '{{ $field->tipe_input }}', '{{ addslashes($field->placeholder ?? '') }}', {{ $field->is_required ? 'true' : 'false' }}, {{ $field->urutan }}, '{{ is_array($field->opsi_select) ? implode(', ', $field->opsi_select) : '' }}', {{ $layanan->id }})"
                                            class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 hover:bg-amber-100"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                            </svg>
                                        </button>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.layanan.fields.destroy', [$layanan, $field]) }}"
                                            onsubmit="
                                                return confirm(
                                                    'Hapus field ini?',
                                                );
                                            "
                                        >
                                            @csrf
                                            @method ("DELETE")
                                            <button
                                                type="submit"
                                                class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-100"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="5"
                                    class="px-6 py-8 text-center text-sm text-gray-400"
                                >
                                    Belum ada field di section ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tambah Field --}}
            <div class="px-4 py-3 border-t border-dashed border-gray-200">
                <button
                    onclick="openAddFieldModal({{ $section->id }}, {{ $layanan->id }})"
                    class="w-full py-2.5 text-sm font-medium text-primary bg-primary/5 rounded-xl hover:bg-primary/10 transition flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Field Baru ke Section Ini
                </button>
            </div>
        </div>
    @empty
        <div
            class="bg-white rounded-2xl border border-gray-light p-12 text-center"
        >
            <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <h3 class="text-lg font-bold text-dark mb-2">Belum ada section</h3>
            <p class="text-sm text-gray mb-6">Mulai dengan membuat section pertama untuk form layanan ini.</p>
            <button
                onclick="openModal('sectionModal')"
                class="bg-primary text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-primary-hover transition"
            >
                Buat Section Pertama
            </button>
        </div>
    @endforelse
    {{-- ============================================ --}}
    {{-- MODAL: TAMBAH SECTION --}}
    {{-- ============================================ --}}
    <div
        id="sectionModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('sectionModal')"
        ></div>
        <div
            class="relative bg-white rounded-2xl w-full max-w-[480px] shadow-xl"
        >
            <div class="flex items-center justify-between px-8 pt-6 pb-4">
                <h2 class="text-lg font-extrabold text-dark">
                    Buat Section Baru
                </h2>
                <button
                    onclick="closeModal('sectionModal')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="border-t border-gray-100"></div>
            <form
                method="POST"
                action="{{ route('admin.layanan.sections.store', $layanan) }}"
                class="px-8 py-6"
            >
                @csrf
                <div class="mb-5">
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Nama Section</label
                    >
                    <input
                        type="text"
                        name="nama_section"
                        placeholder="Contoh: Alamat & Domisili"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary"
                    />
                </div>
                <button
                    type="submit"
                    class="w-full bg-primary text-white font-bold text-sm h-[50px] rounded-2xl hover:bg-primary-hover transition uppercase tracking-wider"
                >
                    Tambahkan Section
                </button>
            </form>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- MODAL: TAMBAH FIELD (lebih lebar, 2 kolom) --}}
    {{-- ============================================ --}}
    <div
        id="addFieldModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('addFieldModal')"
        ></div>
        <div
            class="relative bg-white rounded-2xl w-full max-w-[640px] max-h-[90vh] overflow-y-auto shadow-xl"
        >
            {{-- Header --}}
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
            >
                <h2 class="text-lg font-bold text-dark">Konfigurasi Field</h2>
                <button
                    onclick="closeModal('addFieldModal')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- Form --}}
            <form id="addFieldForm" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Kolom Kiri --}}
                    <div class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Label Field</label
                            >
                            <input
                                type="text"
                                name="label"
                                placeholder="Contoh: Nomor NIK"
                                required
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Tipe Field</label
                            >
                            <select
                                name="tipe_input"
                                onchange="toggleOpsi(this, 'add')"
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm text-gray-600 focus:ring-primary focus:border-primary"
                            >
                                <option value="text">Text Input</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="select">
                                    Select / Dropdown
                                </option>
                                <option value="file">File Upload</option>
                                <option value="textarea">Textarea</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Placeholder</label
                            >
                            <input
                                type="text"
                                name="placeholder"
                                placeholder="Masukkan instruksi..."
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary"
                            />
                        </div>
                        <div class="flex items-center gap-8 pt-1">
                            <label
                                class="flex items-center gap-2.5 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    name="is_required"
                                    value="1"
                                    class="w-[18px] h-[18px] rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <span class="text-sm font-medium text-gray-700"
                                    >Wajib Isi</span
                                >
                            </label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    name="urutan"
                                    min="1"
                                    value="1"
                                    class="w-[60px] bg-[#F8FAFC] border border-[#F3F3F3] rounded-lg h-[32px] px-2 text-sm text-center focus:ring-primary focus:border-primary"
                                />
                                <span
                                    class="text-xs font-semibold text-gray-500 uppercase"
                                    >Urutan</span
                                >
                            </div>
                        </div>
                    </div>
                    {{-- Kolom Kanan --}}
                    <div>
                        <div id="addOpsiBox" class="hidden">
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Opsi Dropdown</label
                            >
                            <textarea
                                name="opsi_select"
                                rows="8"
                                placeholder="Pisahkan dengan koma, contoh:&#10;Laki-laki, Perempuan"
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl px-4 py-3 text-sm placeholder-gray-400 resize-none focus:ring-primary focus:border-primary"
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-1">Hanya untuk tipe Select/Dropdown</p>
                        </div>
                        <div
                            id="addPreviewBox"
                            class="bg-[#F8FAFC] rounded-xl p-6 h-full min-h-[200px] flex items-center justify-center"
                        >
                            <p class="text-sm text-gray-400 text-center leading-relaxed">Pilih tipe field untuk melihat pengaturan tambahan.</p>
                        </div>
                    </div>
                </div>
                {{-- Actions --}}
                <div
                    class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100"
                >
                    <button
                        type="button"
                        onclick="closeModal('addFieldModal')"
                        class="px-8 py-2.5 text-sm font-medium text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-primary rounded-xl hover:bg-primary-hover transition"
                    >
                        Simpan Field
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- MODAL: EDIT FIELD --}}
    {{-- ============================================ --}}
    <div
        id="editFieldModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('editFieldModal')"
        ></div>
        <div
            class="relative bg-white rounded-2xl w-full max-w-[640px] max-h-[90vh] overflow-y-auto shadow-xl"
        >
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
            >
                <h2 class="text-lg font-bold text-dark">Edit Field</h2>
                <button
                    onclick="closeModal('editFieldModal')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editFieldForm" method="POST" class="p-6">
                @csrf
                @method ("PUT")
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-5">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Label Field</label
                            >
                            <input
                                type="text"
                                name="label"
                                id="ef_label"
                                required
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Tipe Field</label
                            >
                            <select
                                name="tipe_input"
                                id="ef_tipe"
                                onchange="toggleOpsi(this, 'edit')"
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary"
                            >
                                <option value="text">Text Input</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="select">
                                    Select / Dropdown
                                </option>
                                <option value="file">File Upload</option>
                                <option value="textarea">Textarea</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Placeholder</label
                            >
                            <input
                                type="text"
                                name="placeholder"
                                id="ef_placeholder"
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary"
                            />
                        </div>
                        <div class="flex items-center gap-8 pt-1">
                            <label
                                class="flex items-center gap-2.5 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    name="is_required"
                                    id="ef_required"
                                    value="1"
                                    class="w-[18px] h-[18px] rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <span class="text-sm font-medium text-gray-700"
                                    >Wajib Isi</span
                                >
                            </label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    name="urutan"
                                    id="ef_urutan"
                                    min="1"
                                    class="w-[60px] bg-[#F8FAFC] border border-[#F3F3F3] rounded-lg h-[32px] px-2 text-sm text-center focus:ring-primary focus:border-primary"
                                />
                                <span
                                    class="text-xs font-semibold text-gray-500 uppercase"
                                    >Urutan</span
                                >
                            </div>
                        </div>
                    </div>
                    <div>
                        <div id="editOpsiBox" class="hidden">
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2"
                                >Opsi Dropdown</label
                            >
                            <textarea
                                name="opsi_select"
                                id="ef_opsi"
                                rows="8"
                                placeholder="Pisahkan dengan koma"
                                class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl px-4 py-3 text-sm resize-none focus:ring-primary focus:border-primary"
                            ></textarea>
                        </div>
                        <div
                            id="editPreviewBox"
                            class="bg-[#F8FAFC] rounded-xl p-6 h-full min-h-[200px] flex items-center justify-center"
                        >
                            <p class="text-sm text-gray-400 text-center leading-relaxed">Pilih tipe field untuk melihat pengaturan tambahan.</p>
                        </div>
                    </div>
                </div>
                <div
                    class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100"
                >
                    <button
                        type="button"
                        onclick="closeModal('editFieldModal')"
                        class="px-8 py-2.5 text-sm font-medium text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-primary rounded-xl hover:bg-primary-hover transition"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- JAVASCRIPT --}}
    {{-- ============================================ --}}
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove("hidden");
            document.body.style.overflow = "hidden";
        }
        function closeModal(id) {
            document.getElementById(id).classList.add("hidden");
            document.body.style.overflow = "";
        }

        function toggleOpsi(selectEl, prefix) {
            const opsiBox = document.getElementById(prefix + "OpsiBox");
            const previewBox = document.getElementById(prefix + "PreviewBox");
            if (selectEl.value === "select") {
                opsiBox.classList.remove("hidden");
                previewBox.classList.add("hidden");
            } else {
                opsiBox.classList.add("hidden");
                previewBox.classList.remove("hidden");
            }
        }

        function openAddFieldModal(sectionId, layananId) {
            const form = document.getElementById("addFieldForm");
            form.action =
                "/admin/layanan/" +
                layananId +
                "/sections/" +
                sectionId +
                "/fields";
            form.querySelectorAll('input[type="text"], textarea').forEach(
                (el) => (el.value = ""),
            );
            form.querySelector('select[name="tipe_input"]').value = "text";
            form.querySelector('input[name="urutan"]').value = "1";
            form.querySelector('input[name="is_required"]').checked = false;
            document.getElementById("addOpsiBox").classList.add("hidden");
            document.getElementById("addPreviewBox").classList.remove("hidden");
            openModal("addFieldModal");
        }

        function openEditFieldModal(
            fieldId,
            label,
            tipe,
            placeholder,
            isRequired,
            urutan,
            opsi,
            layananId,
        ) {
            document.getElementById("editFieldForm").action =
                "/admin/layanan/" + layananId + "/fields/" + fieldId;
            document.getElementById("ef_label").value = label;
            document.getElementById("ef_tipe").value = tipe;
            document.getElementById("ef_placeholder").value = placeholder;
            document.getElementById("ef_required").checked = isRequired;
            document.getElementById("ef_urutan").value = urutan;
            document.getElementById("ef_opsi").value = opsi;
            if (tipe === "select") {
                document
                    .getElementById("editOpsiBox")
                    .classList.remove("hidden");
                document
                    .getElementById("editPreviewBox")
                    .classList.add("hidden");
            } else {
                document.getElementById("editOpsiBox").classList.add("hidden");
                document
                    .getElementById("editPreviewBox")
                    .classList.remove("hidden");
            }
            openModal("editFieldModal");
        }

        setTimeout(() => {
            const f = document.getElementById("flash-msg");
            if (f) f.style.display = "none";
        }, 3000);
    </script>

@endsection


