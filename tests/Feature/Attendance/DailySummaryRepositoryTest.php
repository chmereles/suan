<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Attendance\Models\SuanDailySummary;
use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use Carbon\Carbon;

uses(RefreshDatabase::class);

it('retrieves daily summaries for a labor link within a date range', function () {

    // ---------------------------
    // 1) Crear un labor link
    // ---------------------------
    $link = SuanLaborLink::factory()->create();

    // ---------------------------
    // 2) Crear daily summaries
    //    (solo algunos deben entrar en el rango)
    // ---------------------------
    SuanDailySummary::factory()->create([
        'labor_link_id' => $link->id,
        'date' => '2025-01-05',
        'status' => 'present',
    ]);

    SuanDailySummary::factory()->create([
        'labor_link_id' => $link->id,
        'date' => '2025-01-10',
        'status' => 'absent_unjustified',
    ]);

    SuanDailySummary::factory()->create([
        'labor_link_id' => $link->id,
        'date' => '2025-02-01',   // fuera del rango
        'status' => 'present',
    ]);

    // Otro labor link (NO debe aparecer)
    $other = SuanLaborLink::factory()->create();

    SuanDailySummary::factory()->create([
        'labor_link_id' => $other->id,
        'date' => '2025-01-10',
        'status' => 'present',
    ]);

    // ---------------------------
    // 3) Ejecutar repositorio
    // ---------------------------
    $repo = app(DailySummaryRepositoryInterface::class);

    $results = $repo->getByLaborLinkBetweenDates(
        $link->id,
        '2025-01-01',
        '2025-01-31'
    );

    // ---------------------------
    // 4) Aserciones
    // ---------------------------

    // Deben venir exactamente 2 registros (05 y 10 de Enero)
    expect($results)->toHaveCount(2);

    // Verificar que las fechas son las correctas
    $dates = array_column($results, 'date');

    // expect($dates)->toContain('2025-01-05');
    // expect($dates)->toContain('2025-01-10');
    // Opción 1: Usar substr para extraer solo la fecha
    expect(substr($dates[0], 0, 10))->toBe('2025-01-05');
    expect(substr($dates[1], 0, 10))->toBe('2025-01-10');

    // Verificar que NO está Febrero
    expect($dates)->not->toContain('2025-02-01');
});
