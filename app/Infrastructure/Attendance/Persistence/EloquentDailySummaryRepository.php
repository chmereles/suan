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

    public function createOrUpdate(array $data): SuanDailySummary
    {
        return SuanDailySummary::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'date' => $data['date'],
            ],
            $data
        );
    }

    public function forRange(string $from, string $to): iterable
    {
        return SuanDailySummary::whereBetween('date', [$from, $to])->get();
    }
}
