@extends('layouts.admin')
@section('title', 'Database Warga')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-[28px] lg:text-[32px] font-extrabold text-dark">Database Warga</h1>
    <form method="GET" action="{{ route('admin.warga.index') }}" class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email/nik" class="w-full sm:w-[350px] bg-white border border-gray-200 rounded-full h-[50px] pl-12 pr-4 text-sm placeholder-gray-400 focus:ring-primary focus:border-primary">
    </form>
</div>

@if(session('success'))
<div id="flash-msg" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
@endif

@if(session('error'))
<div id="flash-error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-2xl border border-gray-light overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[80px]">No</th>
                    <th class="text-left text-sm font-semibold text-gray-500 px-6 py-4">Nama & NIK</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[150px]">Role</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[170px]">Status</th>
                    <th class="text-center text-sm font-semibold text-gray-500 px-6 py-4 w-[150px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warga as $i => $user)
                @php
                    $isPendingVerification = ! $user->is_verified;
                    $accountStatus = $user->status_akun ?? 'aktif';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                    <td class="text-center text-sm text-gray-500 px-6 py-5">{{ $warga->firstItem() + $i }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary shrink-0">
                                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->nama_lengkap)[1] ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-dark">{{ $user->nama_lengkap }}</p>
                                <p class="text-xs text-gray">{{ $user->nik ?: '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-center px-6 py-5">
                        <form method="POST" action="{{ route('admin.warga.role', $user) }}" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <select
                                name="role"
                                onchange="handleRoleChange(this.form, this)"
                                data-current-role="{{ $user->role }}"
                                class="h-[36px] rounded-full border border-transparent px-4 pr-8 text-xs font-bold text-center outline-none transition focus:border-primary {{ $user->role === 'rt' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' }}"
                            >
                                <option value="rt" {{ $user->role === 'rt' ? 'selected' : '' }}>RT</option>
                                <option value="warga" {{ $user->role === 'warga' ? 'selected' : '' }}>Warga</option>
                            </select>
                        </form>
                    </td>
                    <td class="text-center px-6 py-5">
                        @if($isPendingVerification)
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold text-amber-600">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Perlu Verifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold {{ $accountStatus === 'aktif' ? 'text-green-600' : 'text-red-500' }}">
                                <span class="w-2 h-2 rounded-full {{ $accountStatus === 'aktif' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                {{ $accountStatus === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center px-6 py-5">
                        <div class="flex items-center justify-center">
                            <a href="{{ route('admin.warga.show', $user) }}" class="text-sm font-semibold text-primary hover:underline">Detail Profil</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-sm text-gray py-12">Belum ada data warga.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">Menampilkan {{ $warga->firstItem() ?? 0 }} - {{ $warga->lastItem() ?? 0 }} dari {{ $warga->total() }} data</p>
        <div class="flex gap-2">
            @if(!$warga->onFirstPage())<a href="{{ $warga->previousPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Prev</a>@endif
            @if($warga->hasMorePages())<a href="{{ $warga->nextPageUrl() }}" class="px-4 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Next</a>@endif
        </div>
    </div>
</div>

<script>
function handleRoleChange(form, select) {
    const previousRole = select.dataset.currentRole;
    const selectedLabel = select.options[select.selectedIndex].text;

    if (!confirm(`Ubah role pengguna menjadi ${selectedLabel}?`)) {
        select.value = previousRole;
        return;
    }

    form.submit();
}

setTimeout(() => {
    const flashSuccess = document.getElementById('flash-msg');
    const flashError = document.getElementById('flash-error');
    if (flashSuccess) flashSuccess.style.display = 'none';
    if (flashError) flashError.style.display = 'none';
}, 3000);
</script>
@endsection
