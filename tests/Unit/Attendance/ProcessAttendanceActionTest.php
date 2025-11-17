<?php

use App\Domain\Attendance\Actions\ProcessAttendanceAction;
use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('processes raw logs into processed attendance records', function () {

    $link = SuanLaborLink::factory()->create([
        'device_user_id' => 'ABC123'
    ]);

    $logs = new Collection([
        (object)[
            'recorded_at' => '2025-01-10 08:02:00',
            'raw_payload' => '{}',
            'raw_id' => '1'
        ],
        (object)[
            'recorded_at' => '2025-01-10 12:59:00',
            'raw_payload' => '{}',
            'raw_id' => '2'
        ],
    ]);

    $action = app(ProcessAttendanceAction::class);

    $action->execute(
        deviceUserId: 'ABC123',
        date: '2025-01-10',
        logs: $logs
    );

    $records = DB::table('suan_attendance_records')->get();

    expect($records)->toHaveCount(2);
});
