@extends('layouts.app')
@section('title','Pelayanan Desa Dukuhtengah')
@section('content')
<div class="text-center py-20">
    <h1 class="text-4xl font-bold mb-4">Urus Administrasi Tanpa Perlu Antre</h1>
    <p class="text-gray-600 mb-8 max-w-xl mx-auto">Platform pelayanan mandiri untuk warga desa Dukuhtengah.</p>
    @guest
    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Mulai Sekarang</a>
    @endguest
</div>
@endsection