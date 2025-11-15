<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanAnomaly;

interface AnomalyRepositoryInterface
{
    public function unresolvedForEmployeeAndDate(int $employeeId, string $date): iterable;

    public function create(array $data): SuanAnomaly;

    public function resolve(int $id, int $resolvedBy): bool;
}
