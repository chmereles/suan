<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanEmployee;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;

class EloquentEmployeeRepository implements EmployeeRepositoryInterface
{
    public function findById(int $id): ?SuanEmployee
    {
        return SuanEmployee::find($id);
    }

    public function findByLegajo(string $legajo): ?SuanEmployee
    {
        return SuanEmployee::where('legajo', $legajo)->first();
    }

    public function findByDeviceUserId(string $deviceId): ?SuanEmployee
    {
        return SuanEmployee::where('device_user_id', $deviceId)->first();
    }

    public function allActive(): iterable
    {
        return SuanEmployee::active()->get();
    }

    public function createOrUpdate(array $data): SuanEmployee
    {
        return SuanEmployee::updateOrCreate(
            ['legajo' => $data['legajo']],
            $data
        );
    }

    public function allMapped(): array
    {
        return SuanEmployee::whereNotNull('device_user_id')
            ->orderBy('full_name')
            ->get()
            ->all();
    }
}
