@extends('layouts.guest')

@section('title', 'Buat Akun Baru')

@section('content')
<div class="min-h-[calc(100vh-91px)] flex items-center justify-center bg-gradient-to-b from-[#D8E7FF] via-[#DDEAFF] to-white py-12 px-4">
    <div class="w-full max-w-[445px]">
        <div class="bg-white rounded-[27px] shadow-lg px-9 py-10">

            {{-- Logo --}}
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo-tegal.png') }}" alt="Logo" class="h-[63px] w-[51px] object-contain">
            </div>

            {{-- Header --}}
            <div class="text-center mb-6">
                <h2 class="text-[26px] font-extrabold text-black leading-7">Buat Akun Baru</h2>
                <p class="text-base font-normal text-gray mt-2 leading-7">
                    Daftar sekarang untuk menikmati kemudahan<br>layanan mandiri.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                           placeholder="Masukkan Nama Lengkap"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-[18px] h-[51px] px-4 text-sm text-black placeholder-black/30 focus:ring-primary focus:border-primary"
                           required>
                    @error('nama_lengkap')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIK --}}
                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                           placeholder="Masukkan 16 digit NIK"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-[18px] h-[51px] px-4 text-sm text-black placeholder-black/30 focus:ring-primary focus:border-primary"
                           required>
                    @error('nik')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="Masukkan Email"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-[18px] h-[51px] px-4 text-sm text-black placeholder-black/30 focus:ring-primary focus:border-primary"
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No. Telp --}}
                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">No. Telp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                           placeholder="08 xxxxxxxxxxxx"
                           class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-[18px] h-[51px] px-4 text-sm text-black placeholder-black/30 focus:ring-primary focus:border-primary"
                           required>
                    @error('no_hp')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kata Sandi --}}
                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password" id="reg-password"
                               placeholder="Masukkan Kata Sandi"
                               class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-[18px] h-[51px] px-4 pr-12 text-sm text-black placeholder-black/30 focus:ring-primary focus:border-primary"
                               required>
                        <button type="button" onclick="toggleRegPassword('reg-password', 'eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye1" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="reg-password-confirm"
                               placeholder="Konfirmasi Kata Sandi"
                               class="w-full bg-[#F8FAFC] border border-[#F3F3F3] rounded-[18px] h-[51px] px-4 pr-12 text-sm text-black placeholder-black/30 focus:ring-primary focus:border-primary"
                               required>
                        <button type="button" onclick="toggleRegPassword('reg-password-confirm', 'eye2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye2" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="bg-[#EFF6FF] border border-[#BFDBFE] rounded-[18px] p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <p class="text-xs text-primary leading-5">
                        Data Anda akan diverifikasi oleh sistem kependudukan desa untuk memastikan keaslian identitas sebelum akun diaktifkan.
                    </p>
                </div>

                {{-- Tombol Daftar --}}
                <button type="submit"
                        class="w-full bg-primary text-white font-semibold text-sm h-[54px] rounded-[18px] hover:bg-primary-hover transition">
                    Daftar Akun
                </button>

                {{-- Link Masuk --}}
                <p class="text-center text-sm text-gray leading-7">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
function toggleRegPassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
    }
}
</script>
@endsection