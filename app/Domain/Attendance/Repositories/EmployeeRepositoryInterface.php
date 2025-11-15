<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanEmployee;

interface EmployeeRepositoryInterface
{
    public function findById(int $id): ?SuanEmployee;

    public function findByLegajo(string $legajo): ?SuanEmployee;

    public function findByDeviceUserId(string $deviceId): ?SuanEmployee;

    public function allActive(): iterable;

    public function createOrUpdate(array $data): SuanEmployee;
}
