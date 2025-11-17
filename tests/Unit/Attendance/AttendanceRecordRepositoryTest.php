<?php

use App\Infrastructure\Attendance\Persistence\EloquentAttendanceRecordRepository;
use App\Domain\Attendance\DTO\ProcessedRecordDTO;
use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('stores and retrieves attendance records by labor link', function () {
        // Simplified test to isolate the issue
        $link = SuanLaborLink::factory()->create();

        dd($link);
});
