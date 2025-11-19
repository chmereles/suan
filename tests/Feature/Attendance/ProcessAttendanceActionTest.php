<?php

use App\Domain\Attendance\Actions\ProcessAttendanceAction;
use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Models\SuanPerson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('processes raw logs into processed attendance records', function () {

    $person = SuanPerson::factory()->create([
        'device_user_id' => 'ABC123',
    ]);
    SuanLaborLink::factory()->create([
        'person_id' => $person->id,
        'active' => true,
    ]);

    $logs = new Collection([
        (object) [
            'recorded_at' => '2025-01-10 08:02:00',
        ],
        (object) [
            'recorded_at' => '2025-01-10 12:59:00',
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

it('no procesar si no existe persona', function () {

    $logs = new Collection([
        (object) [
            'recorded_at' => '2025-01-10 08:02:00',
        ],
        (object) [
            'recorded_at' => '2025-01-10 12:59:00',
        ],
    ]);

    $action = app(ProcessAttendanceAction::class);

    $action->execute(
        deviceUserId: 'ABC123',
        date: '2025-01-10',
        logs: $logs
    );

    $records = DB::table('suan_attendance_records')->get();

    expect($records)->toHaveCount(0);
});

it('no procesar si no tiene vinculos laborales', function () {

    SuanPerson::factory()->create([
        'device_user_id' => 'ABC123',
    ]);

    $logs = new Collection([
        (object) [
            'recorded_at' => '2025-01-10 08:02:00',
        ],
        (object) [
            'recorded_at' => '2025-01-10 12:59:00',
        ],
    ]);

    $action = app(ProcessAttendanceAction::class);

    $action->execute(
        deviceUserId: 'ABC123',
        date: '2025-01-10',
        logs: $logs
    );

    $records = DB::table('suan_attendance_records')->get();

    expect($records)->toHaveCount(0);
});
