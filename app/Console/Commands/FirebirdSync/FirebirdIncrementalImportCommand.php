<?php

namespace App\Console\Commands\FirebirdSync;

use App\Infrastructure\FirebirdSync\Readers\JsonIncrementalReader;
use App\Domain\FirebirdSync\Services\IncrementalSyncService;
use Illuminate\Console\Command;

class FirebirdIncrementalImportCommand extends Command
{
    protected $signature = 'suan:firebird-incremental-import {file}';
    protected $description = 'Importar archivo incremental Firebird JSON';

    public function handle(
        JsonIncrementalReader $reader,
        IncrementalSyncService $service
    ) {
        $file = $this->argument('file');

        $logs = $reader->read($file);

        foreach ($logs as $log) {
            $service->apply($log);
        }

        $this->info("Incremental procesado.");
    }
}
