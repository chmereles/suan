<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanDailySummary;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;

class EloquentDailySummaryRepository implements DailySummaryRepositoryInterface
{
    public function findByEmployeeAndDate(int $employeeId, string $date): ?SuanDailySummary
    {
        return SuanDailySummary::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();
    }

    public function storeOrUpdate(int $employeeId, string $date, array $data): SuanDailySummary
    {
        return SuanDailySummary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'date' => $date,
            ],
            $data
        );
    }
}
