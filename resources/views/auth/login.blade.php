@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="max-w-auto mt-10">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Masuk</h2>
        @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded-lg px-4 py-3" required>
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message}}</p>@enderror
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded-lg px-4 py-3" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-medium">Masuk</button>
        </form>
        <p class="text-center text-sm mt-4">Belum punya akun?<a href="{{ route('register') }}" class="text-blue-600"> Daftar</a></p>
    </div>
</div>
@endsection