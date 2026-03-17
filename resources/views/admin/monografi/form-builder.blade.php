@extends('layouts.admin')

@section('title', 'Form Builder Monografi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.monografi.index') }}" class="mt-1 w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl lg:text-[24px] font-extrabold text-dark">Form Builder Monografi</h1>
            <p class="text-sm text-gray mt-1">Kelola kategori dan field yang akan tampil pada form monografi RT.</p>
        </div>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <button onclick="openModal('categoryModal')"
                class="h-[50px] px-5 bg-[#0F172A] text-white font-semibold text-sm rounded-xl flex items-center gap-2 hover:bg-[#1E293B] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambahkan Kategori
        </button>
        <a href="{{ route('admin.monografi.index') }}"
           class="h-[50px] px-5 bg-primary text-white font-semibold text-sm rounded-xl flex items-center gap-2 hover:bg-primary-hover transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Kembali ke Laporan
        </a>
    </div>
</div>

@if(session('success'))
<div id="flash-msg" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center justify-between">
    <span>{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()" class="text-green-500">&times;</button>
</div>
@endif

<div class="mb-5 rounded-2xl border border-dashed border-[#D7E3F8] bg-[#F8FAFC] px-5 py-4">
    <p class="text-sm text-[#64748B]">
        Builder monografi saat ini mendukung tipe field <span class="font-semibold text-dark">text</span>,
        <span class="font-semibold text-dark">number</span>,
        <span class="font-semibold text-dark">date</span>, dan
        <span class="font-semibold text-dark">textarea</span> agar tetap sesuai dengan renderer form RT.
    </p>
</div>

@forelse($categories as $category)
<div class="bg-white rounded-2xl border border-gray-light mb-6 overflow-hidden">
    <div class="flex flex-col gap-3 px-6 py-4 bg-gray-50/50 border-b border-gray-100 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <div>
                <h3 class="text-sm font-bold text-dark">Kategori {{ $category->urutan }}: {{ $category->kode }}. {{ $category->nama_kategori }}</h3>
                <p class="text-xs text-gray mt-1">Jumlah field: {{ $category->fields->count() }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->kode) }}', '{{ addslashes($category->nama_kategori) }}', '{{ addslashes($category->icon ?? '') }}', {{ $category->urutan }})"
                class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 hover:bg-amber-100 transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                </svg>
            </button>
            <form method="POST" action="{{ route('admin.monografi.categories.destroy', $category) }}"
                  onsubmit="return confirm('Hapus kategori ini beserta semua field dan data monografinya?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-100 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[60px]">No</th>
                    <th class="text-left text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3">Label Field</th>
                    <th class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[140px]">Tipe Input</th>
                    <th class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[90px]">Urutan</th>
                    <th class="text-center text-[11px] font-semibold text-primary tracking-wider uppercase px-6 py-3 w-[100px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->fields as $index => $field)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-dark">{{ $field->label }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-block text-[11px] font-bold px-3 py-1 rounded-full {{ $field->tipe_badge_color }}">
                            {{ $field->tipe_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-dark">{{ $field->urutan }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button
                                type="button"
                                onclick="openEditFieldModal({{ $field->id }}, '{{ addslashes($field->label) }}', '{{ $field->tipe_input }}', {{ $field->urutan }})"
                                class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 hover:bg-amber-100"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('admin.monografi.fields.destroy', $field) }}"
                                  onsubmit="return confirm('Hapus field monografi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-100">
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
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">Belum ada field di kategori ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t border-dashed border-gray-200">
        <button onclick="openAddFieldModal({{ $category->id }})"
                class="w-full py-2.5 text-sm font-medium text-primary bg-primary/5 rounded-xl hover:bg-primary/10 transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Field Baru ke Kategori Ini
        </button>
    </div>
</div>
@empty
<div class="bg-white rounded-2xl border border-gray-light p-12 text-center">
    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25A2.25 2.25 0 016 3h7.5a2.25 2.25 0 012.25 2.25v15a.75.75 0 01-1.28.53l-2.72-2.72a.75.75 0 00-1.06 0l-2.72 2.72a.75.75 0 01-1.28-.53v-15z" />
    </svg>
    <h3 class="text-lg font-bold text-dark mb-2">Belum ada kategori monografi</h3>
    <p class="text-sm text-gray mb-6">Mulai dengan membuat kategori pertama untuk form monografi RT.</p>
    <button onclick="openModal('categoryModal')" class="bg-primary text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-primary-hover transition">
        Buat Kategori Pertama
    </button>
</div>
@endforelse

<div id="categoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('categoryModal')"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-[520px] shadow-xl">
        <div class="flex items-center justify-between px-8 pt-6 pb-4">
            <h2 class="text-lg font-extrabold text-dark">Buat Kategori Monografi</h2>
            <button onclick="closeModal('categoryModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="border-t border-gray-100"></div>
        <form method="POST" action="{{ route('admin.monografi.categories.store') }}" class="px-8 py-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Kode</label>
                    <input type="text" name="kode" placeholder="Contoh: A" required
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Icon</label>
                    <input type="text" name="icon" placeholder="opsional"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
                </div>
            </div>
            <div class="mt-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Nama Kategori</label>
                <input type="text" name="nama_kategori" placeholder="Contoh: Potensi Sumber Daya Manusia" required
                       class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
            </div>
            <button type="submit" class="w-full mt-6 bg-primary text-white font-bold text-sm h-[50px] rounded-2xl hover:bg-primary-hover transition uppercase tracking-wider">
                Tambahkan Kategori
            </button>
        </form>
    </div>
</div>

<div id="editCategoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('editCategoryModal')"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-[520px] shadow-xl">
        <div class="flex items-center justify-between px-8 pt-6 pb-4">
            <h2 class="text-lg font-extrabold text-dark">Edit Kategori Monografi</h2>
            <button onclick="closeModal('editCategoryModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="border-t border-gray-100"></div>
        <form id="editCategoryForm" method="POST" class="px-8 py-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Kode</label>
                    <input type="text" name="kode" id="ec_kode" required
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Icon</label>
                    <input type="text" name="icon" id="ec_icon"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>
            <div class="mt-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="ec_nama" required
                       class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
            </div>
            <div class="mt-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Urutan</label>
                <input type="number" name="urutan" id="ec_urutan" min="1"
                       class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-2xl h-[50px] px-4 text-sm focus:ring-primary focus:border-primary">
            </div>
            <button type="submit" class="w-full mt-6 bg-primary text-white font-bold text-sm h-[50px] rounded-2xl hover:bg-primary-hover transition uppercase tracking-wider">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<div id="addFieldModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('addFieldModal')"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-[560px] shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-dark">Konfigurasi Field Monografi</h2>
            <button onclick="closeModal('addFieldModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="addFieldForm" method="POST" class="p-6">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Label Field</label>
                    <input type="text" name="label" placeholder="Contoh: Jumlah Penduduk" required
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Field</label>
                    <select name="tipe_input"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm text-gray-600 focus:ring-primary focus:border-primary">
                        <option value="text">Text Input</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="textarea">Textarea</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Urutan</label>
                    <input type="number" name="urutan" min="1" value="1"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>
            <div class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100">
                <button type="button" onclick="closeModal('addFieldModal')"
                        class="px-8 py-2.5 text-sm font-medium text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition">Batal</button>
                <button type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-primary rounded-xl hover:bg-primary-hover transition">Simpan Field</button>
            </div>
        </form>
    </div>
</div>

<div id="editFieldModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('editFieldModal')"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-[560px] shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-dark">Edit Field Monografi</h2>
            <button onclick="closeModal('editFieldModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="editFieldForm" method="POST" class="p-6">
            @csrf @method('PUT')
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Label Field</label>
                    <input type="text" name="label" id="ef_label" required
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Field</label>
                    <select name="tipe_input" id="ef_tipe"
                            class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary">
                        <option value="text">Text Input</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="textarea">Textarea</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Urutan</label>
                    <input type="number" name="urutan" id="ef_urutan" min="1"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl h-[44px] px-4 text-sm focus:ring-primary focus:border-primary">
                </div>
            </div>
            <div class="flex items-center justify-between mt-6 pt-5 border-t border-gray-100">
                <button type="button" onclick="closeModal('editFieldModal')"
                        class="px-8 py-2.5 text-sm font-medium text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition">Batal</button>
                <button type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white bg-primary rounded-xl hover:bg-primary-hover transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}

function openAddFieldModal(categoryId) {
    const form = document.getElementById('addFieldForm');
    form.action = '/admin/monografi/categories/' + categoryId + '/fields';
    form.reset();
    form.querySelector('select[name=\"tipe_input\"]').value = 'text';
    form.querySelector('input[name=\"urutan\"]').value = '1';
    openModal('addFieldModal');
}

function openEditFieldModal(fieldId, label, tipe, urutan) {
    document.getElementById('editFieldForm').action = '/admin/monografi/fields/' + fieldId;
    document.getElementById('ef_label').value = label;
    document.getElementById('ef_tipe').value = tipe;
    document.getElementById('ef_urutan').value = urutan;
    openModal('editFieldModal');
}

function openEditCategoryModal(categoryId, kode, nama, icon, urutan) {
    document.getElementById('editCategoryForm').action = '/admin/monografi/categories/' + categoryId;
    document.getElementById('ec_kode').value = kode;
    document.getElementById('ec_nama').value = nama;
    document.getElementById('ec_icon').value = icon;
    document.getElementById('ec_urutan').value = urutan;
    openModal('editCategoryModal');
}

setTimeout(() => {
    const f = document.getElementById('flash-msg');
    if (f) f.style.display = 'none';
}, 3000);
</script>
@endsection
