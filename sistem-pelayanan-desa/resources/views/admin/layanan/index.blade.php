@extends ("layouts.admin")

@section ("title", "Manajemen Layanan")

@section ("content")
    {{-- Header --}}
    <div
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
    >
        <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">
            Manajemen Layanan
        </h1>

        <div
            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3"
        >
            {{-- Search --}}
            <form
                method="GET"
                action="{{ route('admin.layanan.index') }}"
                class="relative"
            >
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari Layanan"
                    class="w-full sm:w-[300px] bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary"
                />
            </form>

            {{-- Tombol Tambah --}}
            <button
                onclick="openModal('addModal')"
                class="bg-primary text-white font-bold text-sm h-[50px] px-6 rounded-full flex items-center gap-2 hover:bg-primary-hover transition"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Layanan
            </button>
        </div>
    </div>
    {{-- Flash Message --}}
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
                onclick="document.getElementById('flash-msg').remove()"
                class="text-green-500 hover:text-green-700"
            >
                &times;
            </button>
        </div>
    @endif
    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
        {{-- Desktop Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th
                            class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[80px]"
                        >
                            No
                        </th>
                        <th
                            class="text-left text-sm font-semibold text-gray-500 px-6 py-4"
                        >
                            Nama Layanan
                        </th>
                        <th
                            class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[160px]"
                        >
                            Status
                        </th>
                        <th
                            class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[120px]"
                        >
                            Persyaratan
                        </th>
                        <th
                            class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[180px]"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($layanan as $index => $item)
                        <tr
                            class="border-b border-gray-50 hover:bg-gray-50/50 transition"
                        >
                            <td
                                class="text-center text-sm text-gray-500 px-6 py-5"
                            >
                                {{
                                    $layanan->firstItem() +
                                        $index
                                }}
                            </td>
                            <td
                                class="text-left text-sm font-medium text-dark px-6 py-5"
                            >
                                <div class="flex items-center gap-3">
                                    @if ($item->icon_url)
                                        <img
                                            src="{{ $item->icon_url }}"
                                            class="w-8 h-8 object-contain rounded"
                                            alt=""
                                        />
                                    @else
                                        <span class="flex w-8 h-8 items-center justify-center rounded bg-blue-50 text-primary">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </span>
                                    @endif
                                    {{ $item->nama_layanan }}
                                </div>
                            </td>
                            <td class="text-center px-6 py-5">
                                <form
                                    method="POST"
                                    action="{{ route('admin.layanan.toggle', $item) }}"
                                    class="inline"
                                >
                                    @csrf
                                    @method ("PATCH")
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition
                                {{ $item->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-red-50 text-red-500 hover:bg-red-100' }}"
                                    >
                                        <span
                                            class="w-2 h-2 rounded-full {{ $item->is_active ? 'bg-green-500' : 'bg-red-400' }}"
                                        ></span>
                                        {{
                                            $item->is_active
                                                ? "Aktif"
                                                : "Nonaktif"
                                        }}
                                    </button>
                                </form>
                            </td>
                            <td
                                class="text-center text-sm text-gray-500 px-6 py-5"
                            >
                                {{ $item->persyaratan_count ?? 0 }}
                            </td>
                            <td class="text-center px-6 py-5">
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    {{-- Form Builder --}}
                                    <a
                                        href="{{ route('admin.layanan.form-builder', $item) }}"
                                        title="Form Builder"
                                        class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-primary hover:bg-blue-100 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                                        </svg>
                                    </a>
                                    {{-- Edit --}}
                                    <button
                                        onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->nama_layanan) }}', {{ $item->is_active ? 1 : 0 }})"
                                        title="Edit"
                                        class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 hover:bg-amber-100 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    {{-- Hapus --}}
                                    <button
                                        onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_layanan) }}')"
                                        title="Hapus"
                                        class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-100 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="text-center text-sm text-gray py-12"
                            >
                                Belum ada data layanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="sm:hidden px-4 py-4 space-y-3">
            @forelse ($layanan as $item)
                <div class="border border-gray-100 rounded-xl p-4">
                    <div class="mb-3 flex items-center justify-between gap-3 text-xs text-[#64748B]">
                        <span>Persyaratan</span>
                        <span class="font-semibold text-[#0F172A]">{{ $item->persyaratan_count ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            @if ($item->icon_url)
                                <img
                                    src="{{ $item->icon_url }}"
                                    class="w-8 h-8 object-contain"
                                    alt=""
                                />
                            @else
                                <span class="flex w-8 h-8 items-center justify-center rounded bg-blue-50 text-primary">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </span>
                            @endif
                            <p class="text-sm font-bold text-dark">{{ $item->nama_layanan }}</p>
                        </div>
                        <form
                            method="POST"
                            action="{{ route('admin.layanan.toggle', $item) }}"
                        >
                            @csrf
                            @method ("PATCH")
                            <button
                                type="submit"
                                class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $item->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}"
                            >
                                <span
                                    class="inline-block w-1.5 h-1.5 rounded-full mr-1 {{ $item->is_active ? 'bg-green-500' : 'bg-red-400' }}"
                                ></span>
                                {{
                                    $item->is_active
                                        ? "Aktif"
                                        : "Nonaktif"
                                }}
                            </button>
                        </form>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->nama_layanan) }}', {{ $item->is_active ? 1 : 0 }})"
                            class="flex-1 text-xs font-semibold text-primary bg-blue-50 py-2 rounded-lg hover:bg-blue-100"
                        >
                            Edit
                        </button>
                        <button
                            onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_layanan) }}')"
                            class="flex-1 text-xs font-semibold text-red-500 bg-red-50 py-2 rounded-lg hover:bg-red-100"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-center text-sm text-gray py-8">Belum ada data layanan.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($layanan->hasPages())
            <div
                class="flex items-center justify-between px-6 py-4 border-t border-gray-100"
            >
                <p class="text-sm text-gray-500">Menampilkan {{ $layanan->firstItem() }} - {{ $layanan->lastItem() }} dari {{ $layanan->total() }} data</p>
                <div class="flex items-center gap-2">
                    @if ($layanan->onFirstPage())
                        <span
                            class="px-4 py-1.5 text-xs font-medium text-gray-300 bg-gray-50 rounded-lg"
                            >Prev</span
                        >
                    @else
                        <a
                            href="{{ $layanan->previousPageUrl() }}"
                            class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200"
                            >Prev</a
                        >
                    @endif
                    @if ($layanan->hasMorePages())
                        <a
                            href="{{ $layanan->nextPageUrl() }}"
                            class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200"
                            >Next</a
                        >
                    @else
                        <span
                            class="px-4 py-1.5 text-xs font-medium text-gray-300 bg-gray-50 rounded-lg"
                            >Next</span
                        >
                    @endif
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100">
                <p class="text-sm text-gray-500">Menampilkan {{ $layanan->count() }} dari {{ $layanan->total() }} data</p>
            </div>
        @endif
    </div>
    {{-- ============================================ --}}
    {{-- MODAL: TAMBAH LAYANAN --}}
    {{-- ============================================ --}}
    <div
        id="addModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('addModal')"
        ></div>
        {{-- Content --}}
        <div
            class="relative bg-white rounded-2xl w-full max-w-[538px] max-h-[90vh] overflow-y-auto shadow-xl"
        >
            {{-- Header --}}
            <div
                class="flex items-center justify-between px-8 py-6 border-b border-gray-100"
            >
                <h2 class="text-xl font-extrabold text-dark">
                    Tambah Jenis Layanan
                </h2>
                <button
                    onclick="closeModal('addModal')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- Form --}}
            <form
                method="POST"
                action="{{ route('admin.layanan.store') }}"
                enctype="multipart/form-data"
                class="px-8 py-6 space-y-6"
            >
                @csrf
                {{-- Nama Layanan --}}
                <div>
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Nama Layanan</label
                    >
                    <input
                        type="text"
                        name="nama_layanan"
                        placeholder="Silahkan Masukkan Layanan"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary"
                    />
                    @error ("nama_layanan")
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Upload Icon --}}
                <div>
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Upload Icon (Opsional)</label
                    >
                    <label
                        class="flex flex-col items-center justify-center w-full h-[133px] border-2 border-dashed border-gray-200 rounded-2xl bg-[#F8FAFC] cursor-pointer hover:border-primary/40 transition"
                    >
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75z" />
                        </svg>
                        <p class="text-sm text-gray-400">Upload file atau drag & drop</p>
                        <p class="text-xs text-gray-300 mt-1">Gunakan format PNG, JPG, atau SVG (Maks. 2MB)</p>
                        <input
                            type="file"
                            name="icon"
                            class="hidden"
                            accept=".png,.jpg,.jpeg,.svg"
                        />
                    </label>
                </div>
                {{-- Status --}}
                <div>
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Status</label
                    >
                    <select
                        name="is_active"
                        class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm text-gray-600 focus:ring-primary focus:border-primary"
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-primary text-white font-semibold text-sm h-[50px] rounded-2xl hover:bg-primary-hover transition"
                >
                    Lanjutkan
                </button>
            </form>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- MODAL: EDIT LAYANAN --}}
    {{-- ============================================ --}}
    <div
        id="editModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('editModal')"
        ></div>
        <div
            class="relative bg-white rounded-2xl w-full max-w-[538px] max-h-[90vh] overflow-y-auto shadow-xl"
        >
            <div
                class="flex items-center justify-between px-8 py-6 border-b border-gray-100"
            >
                <h2 class="text-xl font-extrabold text-dark">Edit Layanan</h2>
                <button
                    onclick="closeModal('editModal')"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form
                id="editForm"
                method="POST"
                enctype="multipart/form-data"
                class="px-8 py-6 space-y-6"
            >
                @csrf
                @method ("PUT")
                <div>
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Nama Layanan</label
                    >
                    <input
                        type="text"
                        name="nama_layanan"
                        id="edit_nama"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary"
                    />
                </div>
                <div>
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Upload Icon Baru (Opsional)</label
                    >
                    <label
                        class="flex flex-col items-center justify-center w-full h-[133px] border-2 border-dashed border-gray-200 rounded-2xl bg-[#F8FAFC] cursor-pointer hover:border-primary/40 transition"
                    >
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75z" />
                        </svg>
                        <p class="text-sm text-gray-400">Ganti icon (opsional)</p>
                        <p class="text-xs text-gray-300 mt-1">PNG, JPG, atau SVG (Maks. 2MB)</p>
                        <input
                            type="file"
                            name="icon"
                            class="hidden"
                            accept=".png,.jpg,.jpeg,.svg"
                        />
                    </label>
                </div>
                <div>
                    <label
                        class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3"
                        >Status</label
                    >
                    <select
                        name="is_active"
                        id="edit_status"
                        class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm text-gray-600 focus:ring-primary focus:border-primary"
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <button
                    type="submit"
                    class="w-full bg-primary text-white font-semibold text-sm h-[50px] rounded-2xl hover:bg-primary-hover transition"
                >
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
    {{-- ============================================ --}}
    {{-- MODAL: KONFIRMASI HAPUS --}}
    {{-- ============================================ --}}
    <div
        id="deleteModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/40"
            onclick="closeModal('deleteModal')"
        ></div>
        <div
            class="relative bg-white rounded-2xl w-full max-w-[420px] shadow-xl p-8 text-center"
        >
            <div
                class="w-16 h-16 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-4"
            >
                <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-dark mb-2">Hapus Layanan?</h3>
            <p class="text-sm text-gray mb-6">Layanan "<span id="delete_nama" class="font-semibold"></span>" akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button
                    onclick="closeModal('deleteModal')"
                    class="flex-1 h-[44px] border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition"
                >
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method ("DELETE")
                    <button
                        type="submit"
                        class="w-full h-[44px] bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition"
                    >
                        Hapus
                    </button>
                </form>
            </div>
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
        function openEditModal(id, nama, status) {
            document.getElementById("editForm").action = "/admin/layanan/" + id;
            document.getElementById("edit_nama").value = nama;
            document.getElementById("edit_status").value = status;
            openModal("editModal");
        }
        function openDeleteModal(id, nama) {
            document.getElementById("deleteForm").action =
                "/admin/layanan/" + id;
            document.getElementById("delete_nama").textContent = nama;
            openModal("deleteModal");
        }

        // Auto-hide flash message
        setTimeout(() => {
            const flash = document.getElementById("flash-msg");
            if (flash) flash.style.display = "none";
        }, 3000);

        // Show filename saat upload icon
        document.querySelectorAll('input[type="file"]').forEach((input) => {
            input.addEventListener("change", function () {
                const label = this.closest("label");
                const p = label.querySelector("p");
                if (this.files[0]) {
                    p.textContent = this.files[0].name;
                }
            });
        });
    </script>

@endsection




