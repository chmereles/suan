<?php

namespace App\Infrastructure\FirebirdSync\Readers;

use App\Domain\FirebirdSync\DTO\FullTableDTO;

class JsonFullReader
{
    public function read(string $path): FullTableDTO
    {
        $data = json_decode(file_get_contents($path), true);

        return new FullTableDTO(
            table: $data['table'],
            columns: $data['columns'],
            count: $data['count'],
            generated_at: $data['generated_at'],
            rows: $data['rows']
        );
    }
}
