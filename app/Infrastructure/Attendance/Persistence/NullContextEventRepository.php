<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use Carbon\CarbonInterface;

class NullContextEventRepository implements ContextEventRepositoryInterface
{
    public function hasEventForDate(int $employeeId, CarbonInterface $date): bool
    {
        // Por ahora: no hay eventos cargados en SUAN
        return false;
    }

    public function getEventsForDate(int $employeeId, CarbonInterface $date): array
    {
        return []; // lista vacía por ahora
    }
}
