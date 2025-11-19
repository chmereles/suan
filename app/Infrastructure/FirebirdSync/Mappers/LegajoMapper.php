<?php

namespace App\Infrastructure\FirebirdSync\Mappers;

class LegajoMapper
{
    public function map(array $row): array
    {
        return [
            'external_id' => $row['LEGAJO'],
            'full_name'  => $row['FULLNAME'],
            'document'  => $row['NUM_DOCU'],
            'device_user_id'  => $row['NUM_DOCU'],
            // 'cuil'       => $row['CUIL'],
            // 'email'      => $row['EMAIL'] ?? null,
            // 'birth_date' => $row['FEC_NACIMIENTO'] ?? null,
        ];
    }
}
