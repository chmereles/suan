<?php

use App\Domain\Attendance\Enums\DailyStatus;
use App\Domain\Attendance\Models\SuanAttendanceRecord;
use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Models\SuanDailySummary;
use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Services\DailySummaryResolverService;
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
