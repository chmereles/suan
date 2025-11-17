<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\DTO\ProcessedRecordDTO;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentAttendanceRecordRepository implements AttendanceRecordRepositoryInterface
{
    public function deleteByLaborLinkAndDate(int $employeeId, string $date): void
    {
        DB::table('suan_attendance_records')
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->delete();
    }

    public function store(ProcessedRecordDTO $dto): void
    {
        DB::table('suan_attendance_records')->insert([
            'employee_id' => $dto->laborLinkId,
            'date' => $dto->date,
            'type' => $dto->type,
            'recorded_at' => $dto->recordedAt,
            'attendance_log_id' => $dto->attendanceLogId,
            'raw_id' => $dto->rawId,
            'raw_payload' => json_encode($dto->rawPayload),
            'metadata' => json_encode($dto->metadata),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getByLaborLinkAndDate(int $employeeId, string $date): array
    {
        return DB::table('suan_attendance_records')
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->orderBy('recorded_at')
            ->get()
            ->toArray();
    }
}
