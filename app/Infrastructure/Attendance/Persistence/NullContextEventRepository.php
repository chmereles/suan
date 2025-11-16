<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use App\Domain\Attendance\Models\SuanContextEvent;
use Carbon\CarbonInterface;

class NullContextEventRepository implements ContextEventRepositoryInterface
{
    /**
     * NO guarda datos, solo devuelve un modelo vacío (mock)
     */
    public function store(array $data): SuanContextEvent
    {
        return new SuanContextEvent($data);
    }

    /**
     * No hay eventos en el sistema aún
     */
    public function hasEventForDate(int $employeeId, CarbonInterface $date): bool
    {
        return false;
    }

    /**
     * Devuelve lista vacía para evitar errores
     */
    public function getForEmployeeAndDate(int $employeeId, CarbonInterface $date): array
    {
        return [];
    }
}
