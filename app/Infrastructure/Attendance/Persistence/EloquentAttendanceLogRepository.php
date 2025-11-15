<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Repositories\AttendanceLogRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentAttendanceLogRepository implements AttendanceLogRepositoryInterface
{
    public function getByDeviceUserAndDate(string $deviceUserId, string $date): Collection
    {
        return DB::table('attendance_logs')
            ->where('device_user_id', $deviceUserId)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at')
            ->get();
    }

    public function getRange(string $from, string $to): Collection
    {
        return DB::table('attendance_logs')
            ->whereBetween('recorded_at', [$from, $to])
            ->orderBy('recorded_at')
            ->get();
    }
}
