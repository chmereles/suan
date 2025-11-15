<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanAnomaly;
use App\Domain\Attendance\Repositories\AnomalyRepositoryInterface;

class EloquentAnomalyRepository implements AnomalyRepositoryInterface
{
    public function unresolvedForEmployeeAndDate(int $employeeId, string $date): iterable
    {
        return SuanAnomaly::where('employee_id', $employeeId)
            ->where('date', $date)
            ->where('resolved', false)
            ->get();
    }

    public function create(array $data): SuanAnomaly
    {
        return SuanAnomaly::create($data);
    }

    public function resolve(int $id, int $resolvedBy): bool
    {
        return SuanAnomaly::where('id', $id)
            ->update([
                'resolved' => true,
                'resolved_by' => $resolvedBy,
            ]);
    }
}
