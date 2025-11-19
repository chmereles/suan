<?php

namespace App\Infrastructure\FirebirdSync\Mappers;

class PlaAgentesMapper
{
    public function map(array $row): array
    {
        return [
            'legacy_legajo_id' => $row['LEGAJO'],
            'status' => $row['ESTADO'] ?? 'A',
        ];
    }
}
