<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\Attendance\{
    SyncController,
    AttendanceController,
    DailySummaryController,
    EmployeesController
};


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware(['auth', 'verified']) // y el middleware de rol que uses
//     ->prefix('admin/attendance/sync')
//     ->name('attendance.sync.')
//     ->group(function () {
//         Route::get('/', [SyncController::class, 'index'])->name('index');
//         Route::post('/run', [SyncController::class, 'run'])->name('run');
//     });

// Route::middleware(['auth'])->group(function () {

//     Route::prefix('attendance')->group(function () {

//         // Dashboard principal
//         Route::get('/', function () {
//             return Inertia::render('Attendance/Dashboard');
//         })->name('attendance.dashboard');

//         // Sincronización
//         // Route::get('/sync', SyncController::class)
//         Route::get('/attendance/sync', [SyncController::class, 'index'])
//             ->name('attendance.sync.web');

//         // Procesados
//         Route::get('/records', [AttendanceController::class, 'index'])
//             ->name('attendance.records.web');

//         // Consolidados
//         Route::get('/summary', [DailySummaryController::class, 'index'])
//             ->name('attendance.summary.web');

//         // Empleados
//         Route::get('/employees', [EmployeesController::class, 'index'])
//             ->name('attendance.employees.web');
//     });
// });


Route::middleware(['auth'])->prefix('attendance')->group(function () {

    // --------------------------------------------------
    // Dashboard principal
    // --------------------------------------------------
    Route::get('/', function () {
        return inertia('Attendance/Dashboard');
    })->name('attendance.dashboard');

    // --------------------------------------------------
    // Sync (CrossChex → SUAN)
    // --------------------------------------------------
    Route::get('/sync', [SyncController::class, 'index'])
        ->name('attendance.sync.index');

    Route::post('/sync/run', [SyncController::class, 'run'])
        ->name('attendance.sync.run');

    // --------------------------------------------------
    // Procesamiento de registros crudos → records
    // --------------------------------------------------
    Route::get('/records', [AttendanceController::class, 'index'])
        ->name('attendance.records');

    Route::post('/records/process', [AttendanceController::class, 'process'])
        ->name('attendance.records.process');

    // --------------------------------------------------
    // Resumen diario consolidado
    // --------------------------------------------------
    Route::get('/summary', [DailySummaryController::class, 'index'])
        ->name('attendance.summary.index');

    Route::post('/summary/resolve', [DailySummaryController::class, 'resolve'])
        ->name('attendance.summary.resolve');

    // --------------------------------------------------
    // Empleados SUAN
    // --------------------------------------------------
    Route::get('/employees', [EmployeesController::class, 'index'])
        ->name('attendance.employees.index');

    Route::post('/employees/map-device', [EmployeesController::class, 'mapDeviceUserId'])
        ->name('attendance.employees.map-device');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/admin/attendance.php';
