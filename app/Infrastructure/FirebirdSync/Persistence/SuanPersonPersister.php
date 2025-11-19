<?php

namespace App\Infrastructure\FirebirdSync\Persistence;

use App\Domain\Attendance\Models\SuanPerson;

class SuanPersonPersister
{
    public function save(array $data): SuanPerson
    {
        return SuanPerson::updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }
}
