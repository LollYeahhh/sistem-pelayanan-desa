@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-[calc(100vh-91px)] flex items-center justify-center bg-gradient-to-b from-[#D8E7FF] via-[#DDEAFF] to-white py-12">
    <div class="w-full max-w-[445px] mx-4">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center text-dark mb-2">Buat Akun Baru</h2>
            <p class="text-center text-sm text-gray mb-8">Daftar untuk mulai menggunakan layanan desa</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-dark mb-2">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                    @error('nama_lengkap')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-dark mb-2">NIK (16 digit)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                    @error('nik')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-dark mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-dark mb-2">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                    @error('no_hp')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-dark mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-medium text-dark mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                </div>
                <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary-hover font-bold text-base transition">
                    Daftar
                </button>
            </form>

            <p class="text-center text-sm mt-6 text-gray">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection