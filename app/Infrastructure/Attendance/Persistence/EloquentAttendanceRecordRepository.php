<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanAttendanceRecord;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;

class EloquentAttendanceRecordRepository implements AttendanceRecordRepositoryInterface
{
    public function forEmployeeAndDate(int $employeeId, string $date): ?SuanAttendanceRecord
    {
        return SuanAttendanceRecord::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();
    }

    public function create(array $data): SuanAttendanceRecord
    {
        return SuanAttendanceRecord::create($data);
    }

    public function update(SuanAttendanceRecord $record, array $data): SuanAttendanceRecord
    {
        $record->update($data);

        return $record;
    }

    public function between(string $from, string $to, int $employeeId): iterable
    {
        return SuanAttendanceRecord::where('employee_id', $employeeId)
            ->whereBetween('date', [$from, $to])
            ->get();
    }
}
