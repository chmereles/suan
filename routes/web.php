<?php

use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\ContextEventController;
use App\Http\Controllers\Attendance\DailySummaryController;
use App\Http\Controllers\Attendance\EmployeesController;
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

Route::middleware(['auth'])->prefix('attendance')->group(function () {
    // --------------------------------------------------
    // Resumen diario consolidado
    // --------------------------------------------------
    Route::get('/', [DailySummaryController::class, 'index'])
        ->name('attendance.index');

    Route::post('/summary/resolve', [DailySummaryController::class, 'resolve'])
        ->name('attendance.summary.resolve');

    // --------------------------------------------------
    // Procesamiento de registros crudos → records
    // --------------------------------------------------
    Route::get('/records', [AttendanceController::class, 'index'])
        ->name('attendance.records');

    Route::post('/records/process', [AttendanceController::class, 'process'])
        ->name('attendance.records.process');

    // --------------------------------------------------
    // Empleados SUAN
    // --------------------------------------------------
    Route::get('/employees', [EmployeesController::class, 'index'])
        ->name('attendance.employees.index');

    Route::post('/employees/map-device', [EmployeesController::class, 'mapDeviceUserId'])
        ->name('attendance.employees.map-device');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/context-event/create', [ContextEventController::class, 'create'])
        ->name('attendance.context-event.create');

    Route::post('/attendance/context-event', [ContextEventController::class, 'store'])
        ->name('attendance.context-event.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin/attendance.php';
