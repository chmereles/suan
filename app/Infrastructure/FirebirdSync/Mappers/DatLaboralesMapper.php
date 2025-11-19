<?php

namespace App\Infrastructure\FirebirdSync\Mappers;

class DatLaboralesMapper
{
    public function map(array $row): array
    {
        return [
            'person_id' => $row['LEGAJO'],
            'source' => 'haberes',
            'area' => $row['REFLOCALI'],
            'payment_method' => $row['PAGO'] ?? null,
        ];
    }
}
