<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Services\DailySummaryResolverService;

/**
 * Caso de uso: resolver el estado final del día para un empleado.
 */
class ResolveDailySummaryAction
{
    public function __construct(
        private DailySummaryResolverService $resolver
    ) {}

    /**
     * @param  string  $date  → YYYY-MM-DD
     */
    public function execute(int $employeeId, string $date)
    {
        return $this->resolver->resolve($employeeId, $date);
    }
}
