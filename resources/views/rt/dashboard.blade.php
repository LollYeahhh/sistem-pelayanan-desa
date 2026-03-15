@extends('layouts.app')
@section('title', 'Dashboard RT')
@section('content')

<h1 class="text-2xl font-bold text-dark">Dashboard RT</h1>
<p class="mt-2 text-gray">Selamat datang, {{ auth()->user()->nama_lengkap }}!</p>
@endsection