<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ThresholdController;
use App\Http\Controllers\DeviceControlController;

// Sensor data routes
Route::prefix('sensors')->group(function () {
    Route::post('/', [SensorController::class, 'store']);          // POST /api/sensors
    Route::get('/latest', [SensorController::class, 'latest']);    // GET /api/sensors/latest
    Route::get('/history', [SensorController::class, 'history']);  // GET /api/sensors/history
});

// Threshold routes
Route::get('/threshold', [ThresholdController::class, 'get']);     // GET /api/threshold
Route::post('/threshold', [ThresholdController::class, 'update']); // POST /api/threshold

// Device control route
Route::post('/device/control', [DeviceControlController::class, 'control']); // POST /api/device/control
