<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanDailySummary;

interface DailySummaryRepositoryInterface
{
    public function findByEmployeeAndDate(int $employeeId, string $date): ?SuanDailySummary;

    public function createOrUpdate(array $data): SuanDailySummary;

    public function forRange(string $from, string $to): iterable;
}
