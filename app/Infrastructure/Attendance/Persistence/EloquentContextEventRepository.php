<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanManualNote;
use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use Carbon\CarbonInterface;

class EloquentContextEventRepository implements ContextEventRepositoryInterface
{
    public function store(array $data): SuanContextEvent
    {
        return SuanContextEvent::create($data);
    }

    public function hasEventForDate(int $employeeId, CarbonInterface $date): bool
    {
        return SuanContextEvent::query()
            ->forEmployeeAndDate($employeeId, $date->toDateString())
            ->exists();
    }

    public function getForEmployeeAndDate(int $employeeId, CarbonInterface $date): array
    {
        return SuanContextEvent::query()
            ->forEmployeeAndDate($employeeId, $date->toDateString())
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }
}
