<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

// Halaman utama dashboard yang menampilkan status sensor
Route::get('/', [SensorController::class, 'index'])->name('dashboard');
