<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanPerson;
use App\Domain\Attendance\Repositories\PersonRepositoryInterface;

class EloquentPersonRepository implements PersonRepositoryInterface
{
    public function findById(int $id): ?SuanPerson
    {
        return SuanPerson::find($id);
    }

    public function findByDocument(string $document): ?SuanPerson
    {
        return SuanPerson::where('document', $document)->first();
    }

    public function findByDeviceUserId(string $deviceUserId): ?SuanPerson
    {
        return SuanPerson::where('device_user_id', $deviceUserId)->first();
    }

    public function allWithDeviceUserId(): array
    {
        return SuanPerson::whereNotNull('device_user_id')->get()->toArray();
    }

    public function upsert(array $data): SuanPerson
    {
        return SuanPerson::updateOrCreate(
            ['document' => $data['document']],
            $data
        );
    }

    public function all(bool $withLaborLinks = false): array
    {
        $query = SuanPerson::query();

        if ($withLaborLinks) {
            $query->with('laborLinks');
        }

        return $query->get()->toArray();
    }
}
