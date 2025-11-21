<?php

use App\Domain\Attendance\Enums\DailyStatus;
use App\Domain\Attendance\Models\SuanAttendanceRecord;
use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Models\SuanDailySummary;
use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Services\DailySummaryResolverService;
use App\Domain\Labor\Models\SuanSchedule;
use App\Domain\Labor\Models\SuanScheduleWorkday;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// it('handles night shift across midnight and calculates minutes correctly', function () {

//     // ------------------------------------------------------
//     // 1. Crear LaborLink con turno 22:00 → 06:00
//     // ------------------------------------------------------
//     $link = SuanLaborLink::factory()->create([
//         'schedule' => [
//             'start' => '22:00',
//             'end'   => '06:00',
//         ],
//     ]);

//     // ------------------------------------------------------
//     // 2. Crear registros de asistencia:
//     //    Entrada: 2025-11-17 22:05
//     //    Salida : 2025-11-18 03:00
//     // ------------------------------------------------------
//     SuanAttendanceRecord::create([
//         'labor_link_id' => $link->id,
//         'date' => '2025-11-17',
//         'recorded_at' => '2025-11-17 22:05:00',
//         'type' => 'in',
//         'source' => 'device',
//         'metadata' => [],
//     ]);

//     SuanAttendanceRecord::create([
//         'labor_link_id' => $link->id,
//         'date' => '2025-11-17',
//         'recorded_at' => '2025-11-18 03:00:00',
//         'type' => 'out',
//         'source' => 'device',
//         'metadata' => [],
//     ]);

//     // ------------------------------------------------------
//     // 3. Ejecutar resolver
//     // ------------------------------------------------------
//     $resolver = app(DailySummaryResolverService::class);

//     $resolver->resolve($link->id, '2025-11-17');

//     // ------------------------------------------------------
//     // 4. Obtener resumen final
//     // ------------------------------------------------------
//     $summary = SuanDailySummary::where('labor_link_id', $link->id)
//         ->where('date', '2025-11-17')
//         ->firstOrFail();

//     // ------------------------------------------------------
//     // 5. Aserciones
//     // ------------------------------------------------------

//     // Trabajo real entre 22:05 → 03:00 = 295 min
//     expect($summary->worked_minutes)->toBe(295);

//     // Entrada tarde: 5 min
//     expect($summary->late_minutes)->toBe(5);

//     // Salida temprano:
//     // Turno termina 06:00 del día siguiente → se fue 03:00
//     // Diff = 180 min
//     expect($summary->early_leave_minutes)->toBe(180);

//     // No debe haber licencias ni eventos
//     expect($summary->has_license)->toBe(false);
//     expect($summary->has_context_event)->toBe(false);

//     // Debe haber anomalías (early leave + jornada incompleta)
//     expect($summary->anomalies)->not->toBeEmpty();
// });

it('resolves justified absence when no marks but context event exists', function () {

    $link = SuanLaborLink::factory()->create();

    // Crear evento de contexto (justificación)
    SuanContextEvent::factory()->create([
        'labor_link_id' => $link->id,
        'date' => '2025-01-10',
    ]);

    $service = app(DailySummaryResolverService::class);

    $summary = $service->resolve($link->id, '2025-01-10');

    expect($summary->status)->toBe(DailyStatus::ABSENT_JUSTIFIED);
});

it('resolves no justified absence when no marks', function () {

    $link = SuanLaborLink::factory()->create();

    $service = app(DailySummaryResolverService::class);

    $summary = $service->resolve($link->id, '2025-01-10');

    expect($summary->status)->toBe(DailyStatus::ABSENT_UNJUSTIFIED);
});

it('calculates late minutes correctly using schedule windows', function () {

    // -----------------------------------------
    // 1. Crear vínculo laboral
    // -----------------------------------------
    $link = SuanLaborLink::factory()->create();

    // -----------------------------------------
    // 2. Crear horario
    // -----------------------------------------
    $schedule = SuanSchedule::create([
        'name' => 'Adm Mañana',
        'type' => 'fixed',
        'active' => true,
    ]);

    SuanScheduleWorkday::create([
        'schedule_id' => $schedule->id,
        'weekday' => 1, // lunes
        'segment_index' => 1,
        'start_time' => '07:00',
        'end_time' => '13:00',
        'tolerance_in_minutes' => 10,
        'tolerance_out_minutes' => 10,
        'is_working_day' => true,
    ]);

    $link->update(['schedule_id' => $schedule->id]);

    // -----------------------------------------
    // 3. Crear registros
    // Check-in 07:15 (5 min tarde)
    // Check-out 13:00
    // -----------------------------------------
    SuanAttendanceRecord::create([
        'labor_link_id' => $link->id,
        'date' => '2025-01-06 07:15:00', // tarde
        'recorded_at' => '2025-01-06 07:15:00', // tarde
    ]);

    SuanAttendanceRecord::create([
        'labor_link_id' => $link->id,
        'date' => '2025-01-06 13:00:00',
        'recorded_at' => '2025-01-06 13:00:00',
    ]);

    // -----------------------------------------
    // 4. Resolver
    // -----------------------------------------
    $service = app(DailySummaryResolverService::class);
    $service->resolve($link->id, '2025-01-06');

    $summary = SuanDailySummary::firstOrFail();

    expect($summary->late_minutes)->toBe(5);
    expect($summary->early_leave_minutes)->toBe(0);
});