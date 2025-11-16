<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanContextEvent;
use Carbon\CarbonInterface;

interface ContextEventRepositoryInterface
{
    public function store(array $data): SuanContextEvent;

    public function hasEventForDate(int $employeeId, CarbonInterface $date): bool;

    public function getForEmployeeAndDate(int $employeeId, CarbonInterface $date): array;
}
