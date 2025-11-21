<?php

namespace App\Infrastructure\FirebirdSync\Persistence;

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Models\SuanPerson;

class SuanLaborLinkPersister
{
    public function save(array $data)
    {
        $lookup = $data['person_lookup'];

        // $person = SuanPerson::where('external_id', $data['legajo_legajo'])->first();
        $person = match ($lookup['type']) {
            'external_id' => SuanPerson::where('external_id', $lookup['value'])->first(),
            'document' => SuanPerson::where('document', $lookup['value'])->first(),
            default => throw new \Exception("Tipo de búsqueda de persona inválido")
        };

        if (!$person) {
            throw new \Exception("No existe SuanPerson para {$lookup['type']}={$lookup['value']}");
        }

        SuanLaborLink::updateOrCreate(
            [
                'person_id' => $person->id,
            ],
            [
                'external_id' => $data['external_id'],
                'active' => $data['active'],
                'source' => 'haberes',
                'area' => $data['area'],
                // 'payment_method' => $data['PAGO'] ?? null,
            ]
        );
    }
}
