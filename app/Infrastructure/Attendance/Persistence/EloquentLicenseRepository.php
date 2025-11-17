<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanLicense;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
use Carbon\CarbonInterface;

class EloquentLicenseRepository implements LicenseRepositoryInterface
{
    /**
     * Obtiene una licencia puntual para un vínculo laboral y fecha.
     */
    public function findForLaborLinkAndDate(int $laborLinkId, string $date): ?SuanLicense
    {
        return SuanLicense::where('labor_link_id', $laborLinkId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Crea o actualiza una licencia.
     */
    public function createOrUpdate(array $data): SuanLicense
    {
        return SuanLicense::updateOrCreate(
            [
                'labor_link_id' => $data['labor_link_id'],
                'date'          => $data['date'],
            ],
            $data
        );
    }

    /**
     * Verifica si un vínculo laboral tiene licencia para la fecha.
     */
    public function hasLicenseForDate(int $laborLinkId, CarbonInterface $date): bool
    {
        return SuanLicense::where('labor_link_id', $laborLinkId)
            ->whereDate('date', $date->toDateString())
            ->exists();
    }

    /**
     * Devuelve todas las licencias del vínculo en una fecha.
     */
    public function getLicensesForDate(int $laborLinkId, CarbonInterface $date): array
    {
        return SuanLicense::where('labor_link_id', $laborLinkId)
            ->whereDate('date', $date->toDateString())
            ->get()
            ->toArray();
    }
}
