@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')

<h1 class="text-2xl font-bold">Dashboard Admin</h1>
<p class="mt-2 text-gray-600">Selamat datang, {{ auth()->user()->nama_lengkap}}!</p>
@endsection