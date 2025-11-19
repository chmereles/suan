<?php

namespace App\Infrastructure\FirebirdSync\Persistence;

use App\Domain\Attendance\Models\SuanLaborLink;

class SuanLaborLinkPersister
{
    public function save(array $data)
    {
        SuanLaborLink::updateOrCreate(
            ['legacy_legajo_id' => $data['legacy_legajo_id']],
            $data
        );
    }
}
