<?php

namespace App\Infrastructure\FirebirdSync\Mappers;

class PlaAgentesMapper
{
    public function map(array $row): array
    {
        return [
            'person_lookup' => [
                'type' => 'document',
                'value' => $row['AG_DOC_NUM'],
            ],
            'external_id' => $row['ID'],
            'active' => $this->normalizeBool($row['AG_ACTIVO'] ?? 'N'),
            'source' => 'planes',
            'area' => $row['AG_DEPENDENCIA_ID'],
            'legajo' => $row['ID'],
            'legajo_legajo' => $row['ID'],
        ];
    }

    private function normalizeBool($value): bool
    {
        return strtoupper(trim($value)) === 'S';
    }
}
