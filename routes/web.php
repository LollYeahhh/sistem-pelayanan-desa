<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LayananController as AdminLayananController;
use App\Http\Controllers\Admin\FormBuilderController as AdminFormBuilderController;
use App\Http\Controllers\Admin\WargaController as AdminWargaController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuanController;
use App\Http\Controllers\Admin\ArsipController as AdminArsipController;
use App\Http\Controllers\Admin\MonografiController as AdminMonografiController;
use App\Models\Layanan;
use Illuminate\Support\Facades\Route;

// ==========================================
// LANDING PAGE
// ==========================================
Route::get('/', function () {
    $layanan = Layanan::where('is_active', true)->get();
    return view('landing', compact('layanan'));
})->name('home');

// ==========================================
// AUTH
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ==========================================
// ADMIN
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Layanan
    Route::get('/layanan', [AdminLayananController::class, 'index'])->name('layanan.index');
    Route::post('/layanan', [AdminLayananController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{layanan}', [AdminLayananController::class, 'update'])->name('layanan.update');
    Route::patch('/layanan/{layanan}/toggle', [AdminLayananController::class, 'toggleStatus'])->name('layanan.toggle');
    Route::delete('/layanan/{layanan}', [AdminLayananController::class, 'destroy'])->name('layanan.destroy');

    // Form Builder
    Route::get('/layanan/{layanan}/form-builder', [AdminFormBuilderController::class, 'show'])->name('layanan.form-builder');
    Route::post('/layanan/{layanan}/sections', [AdminFormBuilderController::class, 'storeSection'])->name('layanan.sections.store');
    Route::delete('/layanan/{layanan}/sections/{section}', [AdminFormBuilderController::class, 'destroySection'])->name('layanan.sections.destroy');
    Route::post('/layanan/{layanan}/sections/{section}/fields', [AdminFormBuilderController::class, 'storeField'])->name('layanan.fields.store');
    Route::put('/layanan/{layanan}/fields/{field}', [AdminFormBuilderController::class, 'updateField'])->name('layanan.fields.update');
    Route::delete('/layanan/{layanan}/fields/{field}', [AdminFormBuilderController::class, 'destroyField'])->name('layanan.fields.destroy');

    // Database Warga
    Route::get('/warga', [AdminWargaController::class, 'index'])->name('warga.index');
    Route::get('/warga/{user}', [AdminWargaController::class, 'show'])->name('warga.show');
    Route::put('/warga/{user}', [AdminWargaController::class, 'update'])->name('warga.update');
    Route::patch('/warga/{user}/toggle', [AdminWargaController::class, 'toggleStatus'])->name('warga.toggle');

    // Pengajuan Surat Masuk
    Route::get('/pengajuan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/{permohonan}', [AdminPengajuanController::class, 'show'])->name('pengajuan.show');
    Route::patch('/pengajuan/{permohonan}/approve', [AdminPengajuanController::class, 'approve'])->name('pengajuan.approve');
    Route::patch('/pengajuan/{permohonan}/reject', [AdminPengajuanController::class, 'reject'])->name('pengajuan.reject');

    // Arsip Dokumen
    Route::get('/arsip', [AdminArsipController::class, 'index'])->name('arsip.index');

    // Laporan Monografi
    Route::get('/monografi', [AdminMonografiController::class, 'index'])->name('monografi.index');
});

// ==========================================
// RT
// ==========================================
Route::middleware(['auth', 'role:rt'])->prefix('rt')->name('rt.')->group(function () {
    Route::get('/dashboard', fn() => view('rt.dashboard'))->name('dashboard');
});

// ==========================================
// WARGA
// ==========================================
Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {
    Route::get('/dashboard', fn() => view('warga.dashboard'))->name('dashboard');
});