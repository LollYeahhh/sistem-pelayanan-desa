<?php

use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;

Route::get('/cek-surat/{id}', [SuratController::class, 'cek']);
