<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Support\Collection;

interface LaborLinkRepositoryInterface
{
    /**
     * Buscar un vínculo laboral por ID interno.
     */
    public function findById(int $id): ?SuanLaborLink;

    /**
     * @return Collection<int, SuanLaborLink>
     *                                        Obtener todos los vínculos laborales activos de una persona.
     */
    public function getActiveByPersonId(int $personId): Collection;

    /**
     * Obtener vínculo laboral por external_id y source
     * (ej: Haberes:1234 o Planes:5678).
     */
    public function findBySourceAndExternalId(string $source, string $externalId): ?SuanLaborLink;

    /**
     * Crear o actualizar un vínculo laboral desde la sincronización legacy.
     */
    public function upsert(array $data): SuanLaborLink;

    /**
     * Desactivar (marcar inactivo) los vínculos laborales que ya no aparecen
     * en la sincronización.
     */
    public function deactivateMissingLinks(int $personId, array $validExternalIds): void;

    /**
     * @return Collection<int, SuanLaborLink>
     *                                        Devuelve todos los vínculos laborales activos del sistema.
     */
    public function allActive(): Collection;
}
