<?php

// app/Console/Commands/SyncCrossChexLogsCommand.php

namespace App\Console\Commands\Attendance;

use App\Domain\Attendance\Actions\SyncCrossChexLogsAction;
use App\Domain\Attendance\Services\AttendanceSyncLogger;
use Illuminate\Console\Command;

class SyncCrossChexLogsCommand extends Command
{
    protected $signature = 'attendance:sync-crosschex {--window= : Ventana de sincronización en minutos}';

    protected $description = 'Sincroniza registros de CrossChex Cloud hacia la base de datos local';

    public function handle(
        SyncCrossChexLogsAction $action,
        AttendanceSyncLogger $logger
    ): int {
        $window = $this->option('window');
        $window = $window !== null ? (int) $window : null;

        $this->info('Iniciando sincronización CrossChex...');

        // Logger para CRON
        $logger = new AttendanceSyncLogger('crosschex', 'cron');
        $logger->start($window);

        try {
            $inserted = $action($window);

            $logger->success($inserted);

            $this->info("Sincronización completada. Nuevos registros insertados: {$inserted}");

            return self::SUCCESS;
        } catch (\Throwable $e) {

            $logger->error($e);

            $this->error('Error durante la sincronización: '.$e->getMessage());
            report($e);

            return self::FAILURE;
        }
    }
}
