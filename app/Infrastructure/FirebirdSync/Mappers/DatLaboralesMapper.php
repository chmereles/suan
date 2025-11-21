<?php

namespace App\Infrastructure\FirebirdSync\Mappers;

class DatLaboralesMapper
{
    public function map(array $row): array
    {
        return [
            'person_lookup' => [
                'type' => 'external_id',
                'value' => $row['LEGAJO_LEGAJO'],
            ],
            'external_id' => $row['LEGAJO'],
            'active' => $this->normalizeBool($row['ACTIVO'] ?? 'N'),
            'source' => 'haberes',
            'area' => $row['REFDEPART'],
            'legajo' => $row['LEGAJO'],
            'legajo_legajo' => $row['LEGAJO_LEGAJO'],
        ];
    }

    private function normalizeBool($value): bool
    {
        return strtoupper(trim($value)) === 'S';
    }
}
