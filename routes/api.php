<?php

use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\DailySummaryController;
use App\Http\Controllers\Attendance\EmployeesController;
use App\Http\Controllers\Attendance\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::prefix('suan/attendance')
//     ->middleware(['auth:sanctum'])
//     ->group(function () {

//     // -------------------------------
//     // SYNC desde CrossChex
//     // -------------------------------
//     // Route::get('/sync', SyncController::class)
//     Route::get('/attendance/sync', [SyncController::class, 'index'])
//         ->name('api.attendance.sync.index');

//     // -------------------------------
//     // Procesamiento de registros
//     // -------------------------------
//     Route::post('/process', [AttendanceController::class, 'process'])
//         ->name('attendance.process');

//     Route::get('/records', [AttendanceController::class, 'index'])
//         ->name('attendance.records');

//     // -------------------------------
//     // Daily Summary (consolidados)
//     // -------------------------------
//     Route::get('/summary', [DailySummaryController::class, 'index'])
//         ->name('attendance.summary.index');

//     Route::post('/summary/resolve', [DailySummaryController::class, 'resolve'])
//         ->name('attendance.summary.resolve');

//     // -------------------------------
//     // Gestión de empleados
//     // -------------------------------
//     Route::get('/employees', [EmployeesController::class, 'index'])
//         ->name('attendance.employees');

//     Route::post('/employees/map-device', [EmployeesController::class, 'mapDeviceUserId'])
//         ->name('attendance.employees.map-device');
// });
