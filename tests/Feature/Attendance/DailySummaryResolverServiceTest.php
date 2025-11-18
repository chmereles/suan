<?php

use App\Domain\Attendance\Enums\DailyStatus;
use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Services\DailySummaryResolverService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
