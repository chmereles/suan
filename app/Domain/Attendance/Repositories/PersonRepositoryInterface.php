<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanPerson;

interface PersonRepositoryInterface
{
    /**
     * Buscar una persona por su ID interno.
     */
    public function findById(int $id): ?SuanPerson;

    /**
     * Buscar una persona por DNI / documento.
     */
    public function findByDocument(string $document): ?SuanPerson;

    /**
     * Buscar una persona por su ID biométrico (CrossChex).
     */
    public function findByDeviceUserId(string $deviceUserId): ?SuanPerson;

    /**
     * Listar todas las personas que tienen deviceUserId asignado.
     */
    public function allWithDeviceUserId(): array;

    /**
     * Crear o actualizar una persona al sincronizar datos legacy.
     * (por ejemplo desde Legajos)
     */
    public function upsert(array $data): SuanPerson;

    /**
     * Obtener todas las personas activas, opcionalmente con sus vínculos.
     */
    public function all(bool $withLaborLinks = false): array;
}
