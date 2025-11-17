<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentLaborLinkRepository implements LaborLinkRepositoryInterface
{
    public function findById(int $id): ?SuanLaborLink
    {
        return SuanLaborLink::find($id);
    }

    /**
     * @return Collection<int, SuanLaborLink>
     * Obtener todos los vínculos laborales activos de una persona.
     */
    public function getActiveByPersonId(int $personId): Collection
    {
        return SuanLaborLink::where('person_id', $personId)
            ->where('active', true)
            ->get();
    }

    public function findBySourceAndExternalId(string $source, string $externalId): ?SuanLaborLink
    {
        return SuanLaborLink::where('source', $source)
            ->where('external_id', $externalId)
            ->first();
    }

    public function upsert(array $data): SuanLaborLink
    {
        return SuanLaborLink::updateOrCreate(
            [
                'source'      => $data['source'],
                'external_id' => $data['external_id'],
            ],
            $data
        );
    }

    public function deactivateMissingLinks(int $personId, array $validExternalIds): void
    {
        SuanLaborLink::where('person_id', $personId)
            ->whereNotIn('external_id', $validExternalIds)
            ->update(['active' => false]);
    }

    /**
     * @return Collection<int, SuanLaborLink>
     */
    public function allActive(): Collection
    {
        return SuanLaborLink::where('active', true)->get();
    }
}
