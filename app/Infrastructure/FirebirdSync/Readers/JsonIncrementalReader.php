<?php

namespace App\Infrastructure\FirebirdSync\Readers;

use App\Domain\FirebirdSync\DTO\ChangeDTO;
use App\Domain\FirebirdSync\DTO\IncrementalLogDTO;

class JsonIncrementalReader
{
    public function read(string $path): array
    {
        $data = json_decode(file_get_contents($path), true);

        $logs = [];

        foreach ($data['logs'] as $log) {
            $changes = array_map(
                fn($c) => new ChangeDTO(
                    column: $c['column'],
                    old: $c['old'],
                    new: $c['new']
                ),
                $log['changes']
            );

            $logs[] = new IncrementalLogDTO(
                id: $log['id'],
                table: $log['table'],
                operation: $log['operation'],
                timestamp: $log['timestamp'],
                pk1: $log['pk1'],
                pk2: $log['pk2'],
                pk3: $log['pk3'],
                changes: $changes
            );
        }

        return $logs;
    }
}
