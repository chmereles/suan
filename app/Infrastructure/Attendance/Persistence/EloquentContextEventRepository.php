<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use Carbon\CarbonInterface;

class EloquentContextEventRepository implements ContextEventRepositoryInterface
{
    /**
     * Crear evento de contexto.
     */
    public function store(array $data): SuanContextEvent
    {
        return SuanContextEvent::create($data);
    }

    /**
     * ¿Existe algún evento de contexto para este vínculo y fecha?
     */
    public function hasEventForDate(int $laborLinkId, CarbonInterface $date): bool
    {
        return SuanContextEvent::query()
            ->forLaborLinkAndDate($laborLinkId, $date->toDateString())
            ->exists();
    }

    /**
     * Obtener todos los eventos de un vínculo laboral en una fecha.
     */
    public function getForLaborLinkAndDate(int $laborLinkId, CarbonInterface $date): array
    {
        return SuanContextEvent::query()
            ->forLaborLinkAndDate($laborLinkId, $date->toDateString())
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }
}
