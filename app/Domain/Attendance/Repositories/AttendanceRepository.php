<?php

namespace App\Domain\Attendance\Repositories;

use Illuminate\Support\Facades\DB;

class AttendanceRepository
{
    public function storeMany(array $mapped): int
    {
        if (empty($mapped)) {
            return 0;
        }

        $chunks = array_chunk($mapped, 500);
        $total = 0;

        foreach ($chunks as $chunk) {
            DB::table('attendance_logs')->insertOrIgnore($chunk);

            $total += count($chunk);
        }

        return $total;
    }
}
