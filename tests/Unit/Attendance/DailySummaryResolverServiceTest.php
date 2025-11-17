<?php

use App\Domain\Attendance\Services\DailySummaryResolverService;
use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('resolves justified absence when no marks but context event exists', function () {

    $link = SuanLaborLink::factory()->create();

    // Crear evento de contexto (justificación)
    \App\Domain\Attendance\Models\SuanContextEvent::factory()->create([
        'labor_link_id' => $link->id,
        'date' => '2025-01-10'
    ]);

    $service = app(DailySummaryResolverService::class);

    $summary = $service->resolve($link->id, '2025-01-10');

    expect($summary->status)->toBe('absent_justified');
});
