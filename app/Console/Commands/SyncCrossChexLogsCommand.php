<?php

// app/Console/Commands/SyncCrossChexLogsCommand.php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\SyncCrossChexLogsAction;
use Illuminate\Console\Command;

class SyncCrossChexLogsCommand extends Command
{
    protected $signature = 'attendance:sync-crosschex {--window= : Ventana de sincronización en minutos}';
    protected $description = 'Sincroniza registros de CrossChex Cloud hacia la base de datos local';

    public function handle(SyncCrossChexLogsAction $action): int
    {
        $window = $this->option('window');
        $window = $window !== null ? (int) $window : null;

        $this->info('Iniciando sincronización CrossChex...');

        try {
            $inserted = $action($window);

            $this->info("Sincronización completada. Nuevos registros insertados: {$inserted}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error durante la sincronización: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
