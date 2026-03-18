<?php

use App\Http\Controllers\AduanMasyarakatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AduanMasyarakatController as AdminAduanMasyarakatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LayananController as AdminLayananController;
use App\Http\Controllers\Admin\FormBuilderController as AdminFormBuilderController;
use App\Http\Controllers\Admin\WargaController as AdminWargaController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuanController;
use App\Http\Controllers\Admin\ArsipController as AdminArsipController;
use App\Http\Controllers\Admin\MonografiController as AdminMonografiController;
use App\Http\Controllers\Admin\MonografiFormBuilderController as AdminMonografiFormBuilderController;
use App\Http\Controllers\RT\MonografiController as RTMonografiController;
use App\Http\Controllers\RT\RTController;
use App\Models\Layanan;
use Illuminate\Support\Facades\Route;

// ==========================================
// LANDING PAGE
// ==========================================
Route::get("/", function () {
    $layanan = Layanan::where("is_active", true)->get();
    return view("landing", compact("layanan"));
})->name("home");
Route::post("/aduan", [AduanMasyarakatController::class, "store"])->name(
    "aduan.store",
);

// ==========================================
// AUTH
// ==========================================
Route::middleware("guest")->group(function () {
    Route::get("/login", [AuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "login"]);
    Route::get("/register", [AuthController::class, "showRegister"])->name(
        "register",
    );
    Route::post("/register", [AuthController::class, "register"]);
});

Route::post("/logout", [AuthController::class, "logout"])
    ->middleware("auth")
    ->name("logout");

// ==========================================
// ADMIN
// ==========================================
Route::middleware(["auth", "role:admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        // Dashboard
        Route::get("/dashboard", [
            AdminDashboardController::class,
            "index",
        ])->name("dashboard");

        // Manajemen Layanan
        Route::get("/layanan", [AdminLayananController::class, "index"])->name(
            "layanan.index",
        );
        Route::post("/layanan", [AdminLayananController::class, "store"])->name(
            "layanan.store",
        );
        Route::put("/layanan/{layanan}", [
            AdminLayananController::class,
            "update",
        ])->name("layanan.update");
        Route::patch("/layanan/{layanan}/toggle", [
            AdminLayananController::class,
            "toggleStatus",
        ])->name("layanan.toggle");
        Route::delete("/layanan/{layanan}", [
            AdminLayananController::class,
            "destroy",
        ])->name("layanan.destroy");

        // Form Builder
        Route::get("/layanan/{layanan}/form-builder", [
            AdminFormBuilderController::class,
            "show",
        ])->name("layanan.form-builder");
        Route::post("/layanan/{layanan}/sections", [
            AdminFormBuilderController::class,
            "storeSection",
        ])->name("layanan.sections.store");
        Route::delete("/layanan/{layanan}/sections/{section}", [
            AdminFormBuilderController::class,
            "destroySection",
        ])->name("layanan.sections.destroy");
        Route::post("/layanan/{layanan}/sections/{section}/fields", [
            AdminFormBuilderController::class,
            "storeField",
        ])->name("layanan.fields.store");
        Route::put("/layanan/{layanan}/fields/{field}", [
            AdminFormBuilderController::class,
            "updateField",
        ])->name("layanan.fields.update");
        Route::delete("/layanan/{layanan}/fields/{field}", [
            AdminFormBuilderController::class,
            "destroyField",
        ])->name("layanan.fields.destroy");

        // Database Warga
        Route::get("/warga", [AdminWargaController::class, "index"])->name(
            "warga.index",
        );
        Route::get("/warga/{user}", [
            AdminWargaController::class,
            "show",
        ])->name("warga.show");
        Route::put("/warga/{user}", [
            AdminWargaController::class,
            "update",
        ])->name("warga.update");
        Route::patch("/warga/{user}/role", [
            AdminWargaController::class,
            "updateRole",
        ])->name("warga.role");
        Route::patch("/warga/{user}/verify", [
            AdminWargaController::class,
            "verify",
        ])->name("warga.verify");
        Route::patch("/warga/{user}/toggle", [
            AdminWargaController::class,
            "toggleStatus",
        ])->name("warga.toggle");

        // Pengajuan Surat Masuk
        Route::get("/pengajuan", [
            AdminPengajuanController::class,
            "index",
        ])->name("pengajuan.index");
        Route::get("/pengajuan/{permohonan}", [
            AdminPengajuanController::class,
            "show",
        ])->name("pengajuan.show");
        Route::patch("/pengajuan/{permohonan}/approve", [
            AdminPengajuanController::class,
            "approve",
        ])->name("pengajuan.approve");
        Route::patch("/pengajuan/{permohonan}/reject", [
            AdminPengajuanController::class,
            "reject",
        ])->name("pengajuan.reject");

        // Arsip Dokumen
        Route::get("/arsip", [AdminArsipController::class, "index"])->name(
            "arsip.index",
        );

        // Aduan Masyarakat
        Route::get("/aduan", [AdminAduanMasyarakatController::class, "index"])->name(
            "aduan.index",
        );
        Route::get("/aduan/{aduan}", [
            AdminAduanMasyarakatController::class,
            "show",
        ])->name("aduan.show");
        Route::patch("/aduan/{aduan}/status", [
            AdminAduanMasyarakatController::class,
            "updateStatus",
        ])->name("aduan.status");
        Route::get("/aduan/{aduan}/lampiran", [
            AdminAduanMasyarakatController::class,
            "downloadLampiran",
        ])->name("aduan.lampiran");

        // Laporan Monografi
        Route::get("/monografi", [
            AdminMonografiController::class,
            "index",
        ])->name("monografi.index");
        Route::get("/monografi/form-builder", [
            AdminMonografiFormBuilderController::class,
            "show",
        ])->name("monografi.form-builder");
        Route::post("/monografi/categories", [
            AdminMonografiFormBuilderController::class,
            "storeCategory",
        ])->name("monografi.categories.store");
        Route::put("/monografi/categories/{category}", [
            AdminMonografiFormBuilderController::class,
            "updateCategory",
        ])->name("monografi.categories.update");
        Route::delete("/monografi/categories/{category}", [
            AdminMonografiFormBuilderController::class,
            "destroyCategory",
        ])->name("monografi.categories.destroy");
        Route::post("/monografi/categories/{category}/fields", [
            AdminMonografiFormBuilderController::class,
            "storeField",
        ])->name("monografi.fields.store");
        Route::put("/monografi/fields/{field}", [
            AdminMonografiFormBuilderController::class,
            "updateField",
        ])->name("monografi.fields.update");
        Route::delete("/monografi/fields/{field}", [
            AdminMonografiFormBuilderController::class,
            "destroyField",
        ])->name("monografi.fields.destroy");
    });

// ==========================================
// RT
// ==========================================
Route::middleware(["auth", "role:rt"])
    ->prefix("rt")
    ->name("rt.")
    ->group(function () {
        Route::get("/dashboard", [RTController::class, "dashboard"])->name(
            "dashboard",
        );

        Route::get("/monografi", [RTMonografiController::class, "index"])->name(
            "monografi.index",
        );
        Route::get("/monografi/{category}", [
            RTMonografiController::class,
            "show",
        ])->name("monografi.show");
        Route::put("/monografi/{category}", [
            RTMonografiController::class,
            "update",
        ])->name("monografi.update");

        Route::middleware("verified.active")->group(function () {
        Route::get("/ajukan", [RTController::class, "ajukanIndex"])->name(
            "ajukan.index",
        );
        Route::get("/ajukan/{layanan}", [RTController::class, "ajukanForm"])->name(
            "ajukan.form",
        );
        Route::post("/ajukan/{layanan}", [RTController::class, "ajukanStore"])->name(
            "ajukan.store",
        );
        });

        Route::get("/riwayat", [RTController::class, "riwayat"])->name(
            "riwayat.index",
        );
        Route::get("/riwayat/{permohonan}", [RTController::class, "riwayatShow"])->name(
            "riwayat.show",
        );

        Route::get("/profil", [RTController::class, "profil"])->name("profil");
        Route::put("/profil", [RTController::class, "profilUpdate"])->name(
            "profil.update",
        );
    });

// ==========================================
// WARGA
// ==========================================
Route::middleware(["auth", "role:warga"])
    ->prefix("warga")
    ->name("warga.")
    ->group(function () {
        Route::get("/dashboard", [
            App\Http\Controllers\Warga\WargaController::class,
            "dashboard",
        ])->name("dashboard");

        // Ajukan Layanan (Multi-step)
        Route::middleware("verified.active")->group(function () {
        Route::get("/ajukan", [
            App\Http\Controllers\Warga\WargaController::class,
            "ajukanIndex",
        ])->name("ajukan.index");
        Route::get("/ajukan/{layanan}", [
            App\Http\Controllers\Warga\WargaController::class,
            "ajukanForm",
        ])->name("ajukan.form");
        Route::post("/ajukan/{layanan}", [
            App\Http\Controllers\Warga\WargaController::class,
            "ajukanStore",
        ])->name("ajukan.store");
        });

        // Riwayat & Status
        Route::get("/riwayat", [
            App\Http\Controllers\Warga\WargaController::class,
            "riwayat",
        ])->name("riwayat.index");
        Route::get("/riwayat/{permohonan}", [
            App\Http\Controllers\Warga\WargaController::class,
            "riwayatShow",
        ])->name("riwayat.show");

        // Profil
        Route::get("/profil", [
            App\Http\Controllers\Warga\WargaController::class,
            "profil",
        ])->name("profil");
        Route::put("/profil", [
            App\Http\Controllers\Warga\WargaController::class,
            "profilUpdate",
        ])->name("profil.update");
    });
