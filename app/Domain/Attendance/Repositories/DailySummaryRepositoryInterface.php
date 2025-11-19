<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanDailySummary;

interface DailySummaryRepositoryInterface
{
    /**
     * Obtiene un resumen para vínculo laboral + fecha.
     */
    public function findByLaborLinkAndDate(int $laborLinkId, string $date): ?SuanDailySummary;

    /**
     * Crea o actualiza un resumen diario.
     */
    public function storeOrUpdate(int $laborLinkId, string $date, array $data): SuanDailySummary;

    /**
     * Devuelve todos los resúmenes diarios de una fecha,
     * generalmente para mostrarlos en el dashboard.
     */
    public function getByDate(string $date): array;

    public function getByLaborLinkBetweenDates(int $laborLinkId, string $from, string $to): array;
}
