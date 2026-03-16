@extends('layouts.admin')
@section('title', 'Tinjau Pengajuan')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.pengajuan.index') }}" class="mt-1 w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </a>
        <div>
            <h1 class="text-xl lg:text-[24px] font-extrabold text-dark">Tinjau Pengajuan</h1>
            <p class="text-sm text-gray mt-1">Periksa kelengkapan data dan dokumen sebelum diproses</p>
        </div>
    </div>
    @php
        $statusColors = [
            'menunggu' => 'bg-amber-50 text-amber-600 border-amber-200',
            'diproses' => 'bg-blue-50 text-blue-600 border-blue-200',
            'disetujui' => 'bg-green-50 text-green-600 border-green-200',
            'ditolak' => 'bg-red-50 text-red-500 border-red-200',
        ];
    @endphp
    <span class="inline-block text-xs font-bold px-4 py-2 rounded-full border {{ $statusColors[$permohonan->status] ?? '' }}">
        {{ ucfirst($permohonan->status) }}
    </span>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">

    {{-- Identitas Pemohon --}}
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-2 mb-5">
            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" /></svg>
            <h3 class="text-base font-bold text-dark">Identitas Pemohon</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            @php
                $identitas = [
                    'Nama Lengkap' => $permohonan->user->nama_lengkap ?? '-',
                    'NIK' => $permohonan->user->nik ?? '-',
                    'Tempat Lahir' => $permohonan->user->tempat_lahir ?? '-',
                    'Tanggal Lahir' => isset($permohonan->user->tanggal_lahir) ? date('d/m/Y', strtotime($permohonan->user->tanggal_lahir)) : '-',
                    'Jenis Kelamin' => $permohonan->user->jenis_kelamin ?? '-',
                    'Status Perkawinan' => $permohonan->user->status_perkawinan ?? '-',
                    'Agama' => $permohonan->user->agama ?? '-',
                    'Pekerjaan' => $permohonan->user->pekerjaan ?? '-',
                ];
            @endphp
            @foreach($identitas as $label => $value)
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">{{ $label }}</p>
                <div class="bg-[#F8FAFC] border-b border-gray-200 rounded-t-lg px-4 py-2.5">
                    <p class="text-sm text-dark">{{ $value }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Info Layanan --}}
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-2 mb-5">
            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            <h3 class="text-base font-bold text-dark">Detail Pengajuan</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Jenis Layanan</p>
                <div class="bg-[#F8FAFC] border-b border-gray-200 rounded-t-lg px-4 py-2.5">
                    <p class="text-sm text-dark">{{ $permohonan->layanan->nama_layanan ?? '-' }}</p>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal Pengajuan</p>
                <div class="bg-[#F8FAFC] border-b border-gray-200 rounded-t-lg px-4 py-2.5">
                    <p class="text-sm text-dark">{{ $permohonan->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan (jika ditolak) --}}
    @if($permohonan->status === 'ditolak' && $permohonan->catatan)
    <div class="p-6 border-b border-gray-100">
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-bold text-red-600 mb-1">Alasan Penolakan:</p>
            <p class="text-sm text-red-500">{{ $permohonan->catatan }}</p>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    @if($permohonan->status === 'menunggu' || $permohonan->status === 'diproses')
    <div class="p-6 flex items-center justify-end gap-3">
        <button onclick="openModal('rejectModal')" class="px-6 py-3 border border-red-200 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition">Tolak Dokumen</button>
        <form method="POST" action="{{ route('admin.pengajuan.approve', $permohonan) }}">
            @csrf @method('PATCH')
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-hover transition">Dokumen Valid</button>
        </form>
    </div>
    @endif
</div>

{{-- Modal Tolak --}}
<div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('rejectModal')"></div>
    <div class="relative bg-white rounded-2xl w-full max-w-[500px] shadow-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-dark">Tolak Pengajuan</h2>
            <button onclick="closeModal('rejectModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4 flex gap-3">
            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
            <p class="text-sm text-blue-700">Berikan alasan penolakan agar pemohon dapat memperbaiki dokumen.</p>
        </div>
        <form method="POST" action="{{ route('admin.pengajuan.reject', $permohonan) }}">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan penolakan</label>
                <textarea name="alasan_tolak" rows="5" required placeholder="Contoh: Foto KTP tidak terbaca dengan jelas, silahkan unggah ulang"
                          class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-xl px-4 py-3 text-sm placeholder-gray-400 resize-none focus:ring-primary focus:border-primary"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('rejectModal')" class="flex-1 py-3 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600">Tolak Permohonan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id){document.getElementById(id).classList.remove('hidden');document.body.style.overflow='hidden';}
function closeModal(id){document.getElementById(id).classList.add('hidden');document.body.style.overflow='';}
</script>
@endsection