<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//LANDING PAGE
Route::get('/', function () {
    return view('landing');
})->name('home');

//AUTH (nek belum login)
Route::middleware('guest')->group(function(){
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

//ADMIN
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
});

//RT
Route::middleware(['auth','role:rt'])->prefix('rt')->name('rt.')->group(function(){
    Route::get('/dashboard', fn() => view('rt.dashboard'))->name('dashboard');
});

//WARGA
Route::middleware(['auth','role:warga'])->prefix('warga')->name('warga.')->group(function(){
    Route::get('/dashboard', fn() => view('warga.dashboard'))->name('dashboard');
});
