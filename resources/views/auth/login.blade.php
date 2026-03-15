@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-[calc(100vh-91px)] flex items-center justify-center bg-gradient-to-b from-[#D8E7FF] via-[#DDEAFF] to-white py-12">
    <div class="w-full max-w-[448px] mx-4">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center text-dark mb-8">Masuk</h2>

            @if(session('success'))
                <div class="bg-green-50 text-green-700 border border-green-200 p-3 rounded-xl mb-6 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-dark mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-medium text-dark mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary" required>
                </div>
                <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary-hover font-bold text-base transition">
                    Masuk
                </button>
            </form>

            <p class="text-center text-sm mt-6 text-gray">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Daftar</a>
            </p>
        </div>
    </div>
</div>
@endsection