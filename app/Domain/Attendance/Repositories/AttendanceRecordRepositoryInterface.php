<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanAttendanceRecord;

interface AttendanceRecordRepositoryInterface
{
    public function forEmployeeAndDate(int $employeeId, string $date): ?SuanAttendanceRecord;

    public function create(array $data): SuanAttendanceRecord;

    public function update(SuanAttendanceRecord $record, array $data): SuanAttendanceRecord;

    public function between(string $from, string $to, int $employeeId): iterable;
}
