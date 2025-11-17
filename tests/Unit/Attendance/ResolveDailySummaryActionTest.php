<?php

use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calls resolver service for a labor link', function () {

    $link = SuanLaborLink::factory()->create();

    $action = app(ResolveDailySummaryAction::class);

    $result = $action->execute($link->id, '2025-01-10');

    expect($result)->not()->toBeNull();
});
