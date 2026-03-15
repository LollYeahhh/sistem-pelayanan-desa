@extends('layouts.app')
@section('title', 'Dashboard Warga')
@section('content')

<h1 class="text-2xl font-bold text-dark">Dashboard Warga</h1>
<p class="mt-2 text-gray">Selamat datang, {{ auth()->user()->nama_lengkap }}!</p>
@endsection