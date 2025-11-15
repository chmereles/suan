<?php

use App\Http\Controllers\Attendance\SyncController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified']) // y el middleware de rol que uses
    ->prefix('admin/attendance/sync')
    ->name('attendance.sync.')
    ->group(function () {
        Route::get('/', [SyncController::class, 'index'])->name('index');
        Route::post('/run', [SyncController::class, 'run'])->name('run');
    });

require __DIR__.'/settings.php';
require __DIR__.'/admin/attendance.php';
