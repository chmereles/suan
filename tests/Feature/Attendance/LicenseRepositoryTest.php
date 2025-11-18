<?php

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Infrastructure\Attendance\Persistence\EloquentLicenseRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('detects license for labor link and date', function () {

    $link = SuanLaborLink::factory()->create();

    $repo = app(EloquentLicenseRepository::class);

    $repo->createOrUpdate([
        'labor_link_id' => $link->id,
        'date' => '2025-01-10',
        'type' => 'medical',
        'description' => 'Sick leave',
    ]);

    $hasLicense = $repo->hasLicenseForDate($link->id, Carbon::parse('2025-01-10'));

    expect($hasLicense)->toBeTrue();
});
