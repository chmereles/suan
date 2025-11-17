<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Services\DailySummaryResolverService;

/**
 * Caso de uso: resolver el estado final del día para un vínculo laboral.
 */
class ResolveDailySummaryAction
{
    public function __construct(
        private DailySummaryResolverService $resolver
    ) {}

    /**
     * @param  int    $laborLinkId  → ID del vínculo laboral
     * @param  string $date         → YYYY-MM-DD
     */
    public function execute(int $laborLinkId, string $date)
    {
        return $this->resolver->resolve($laborLinkId, $date);
    }
}
