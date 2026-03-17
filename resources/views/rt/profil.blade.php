@extends ("layouts.rt")
@section ("title", "Profil RT")

@php
    $user = $user ?? auth()->user();
@endphp

@section ("content")
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 lg:p-8">
            <div class="flex items-center gap-5">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-3xl font-bold text-primary">
                    {{ strtoupper(substr($user->nama_lengkap ?? "R", 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-[#0F172A]">{{ $user->nama_lengkap }}</h1>
                    <p class="mt-1 text-sm text-[#64748B]">NIK: {{ $user->nik ?? "-" }}</p>
                    <span class="mt-2 inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                        Role RT
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('rt.profil.update') }}" method="POST">
            @csrf
            @method ("PUT")

            <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 lg:p-8">
                <h2 class="mb-6 text-lg font-bold text-[#0F172A]">Data Pribadi RT</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">NIK</label>
                        <input type="text" value="{{ $user->nik }}" class="h-[48px] w-full cursor-not-allowed rounded-xl border border-gray-200 bg-gray-100 px-4 text-sm text-[#94A3B8]" disabled />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Agama</label>
                        <input type="text" name="agama" value="{{ old('agama', $user->agama) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Status Perkawinan</label>
                        <input type="text" name="status_perkawinan" value="{{ old('status_perkawinan', $user->status_perkawinan) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" class="w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 py-3 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old('alamat', $user->alamat) }}</textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">RT / RW</label>
                        <input type="text" name="rt_rw" value="{{ old('rt_rw', $user->rt_rw) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Desa / Kelurahan</label>
                        <input type="text" name="desa" value="{{ old('desa', $user->desa) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan', $user->kecamatan) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F172A]">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="h-[48px] w-full rounded-xl border border-gray-200 bg-[#F8FAFC] px-4 text-sm text-[#0F172A] transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3 text-sm font-bold text-white shadow-md shadow-primary/30 transition hover:bg-primary-hover">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
