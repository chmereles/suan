<?php

use App\Http\Controllers\Attendance\SyncController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified']) // y el middleware de rol que uses
    ->prefix('admin/attendance/sync')
    ->name('attendance.sync.')
    ->group(function () {
        Route::get('/', [SyncController::class, 'index'])->name('index');
        Route::post('/run', [SyncController::class, 'run'])->name('run');
    });
