<?php

namespace App\Console\Commands\FirebirdSync;

use Illuminate\Console\Command;
use App\Infrastructure\FirebirdSync\Readers\JsonFullReader;
use App\Domain\FirebirdSync\Services\FullSyncService;

class FirebirdFullImportCommand extends Command
{
    protected $signature = 'suan:firebird-full-import {file}';
    protected $description = 'Importar archivo full export Firebird JSON';

    public function handle(JsonFullReader $reader, FullSyncService $service)
    {
        $file = $this->argument('file');

        $dto = $reader->read($file);

        $this->info("Importando tabla: {$dto->table}");

        $service->sync($dto);

        $this->info("Importación completa.");
    }
}
