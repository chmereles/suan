<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use Carbon\CarbonInterface;

/**
 * Null Object implementation of ContextEventRepositoryInterface.
 *
 * This repository performs no persistence and always returns
 * neutral/default values. It is useful in environments where
 * context events are not needed, not implemented, or disabled.
 */
class NullContextEventRepository implements ContextEventRepositoryInterface
{
    /**
     * Does nothing and returns a dummy SuanContextEvent instance.
     */
    public function store(array $data): SuanContextEvent
    {
        // Creamos un modelo vacío que no se guardará en DB
        $event = new SuanContextEvent;
        $event->fill($data ?? []);

        return $event;
    }

    /**
     * Always returns false — no context event exists.
     */
    public function hasEventForDate(int $laborLinkId, CarbonInterface $date): bool
    {
        return false;
    }

    /**
     * Always returns an empty array — no events found.
     */
    public function getForLaborLinkAndDate(int $laborLinkId, CarbonInterface $date): array
    {
        return [];
    }
}
