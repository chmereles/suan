<?php

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Infrastructure\Attendance\Persistence\EloquentContextEventRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('detects context events for a labor link and date', function () {

    $link = SuanLaborLink::factory()->create();

    $repo = app(EloquentContextEventRepository::class);

    $repo->store([
        'labor_link_id' => $link->id,
        'type_id' => 1,
        'date' => '2025-01-10',
        'description' => 'Test',
        'metadata' => [],
    ]);

    $exists = $repo->hasEventForDate($link->id, Carbon::parse('2025-01-10'));

    expect($exists)->toBeTrue();
});
