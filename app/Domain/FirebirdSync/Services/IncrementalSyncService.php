<?php

namespace App\Domain\FirebirdSync\Services;

use App\Domain\FirebirdSync\DTO\IncrementalLogDTO;
use App\Infrastructure\FirebirdSync\Mappers\LegajoMapper;
use App\Infrastructure\FirebirdSync\Mappers\DatLaboralesMapper;
use App\Infrastructure\FirebirdSync\Mappers\PlaAgentesMapper;
use App\Infrastructure\FirebirdSync\Persistence\SuanPersonPersister;
use App\Infrastructure\FirebirdSync\Persistence\SuanLaborLinkPersister;
use App\Infrastructure\FirebirdSync\Persistence\SuanAgentPersister;
use Illuminate\Support\Facades\Log;

class IncrementalSyncService
{
    public function __construct(
        private LegajoMapper $legajoMapper,
        private DatLaboralesMapper $laborMapper,
        private PlaAgentesMapper $agentMapper,

        private SuanPersonPersister $personPersister,
        private SuanLaborLinkPersister $laborPersister,
        private SuanAgentPersister $agentPersister,
    ) {}

    /**
     * Punto principal de entrada.
     */
    public function apply(IncrementalLogDTO $log): void
    {
        try {
            $table = strtoupper($log->table);

            match ($log->operation) {
                'I' => $this->applyInsert($table, $log),
                'U' => $this->applyUpdate($table, $log),
                'D' => $this->applyDelete($table, $log),
                default => Log::warning("Operación desconocida en log Firebird: {$log->operation}")
            };
        } catch (\Throwable $e) {
            Log::error("Error procesando incremental ID {$log->id}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
        }
    }

    /**
     * 1. INSERT
     */
    private function applyInsert(string $table, IncrementalLogDTO $log): void
    {
        $pk = $this->resolvePrimaryKey($log);

        // Para resolver un insert, necesitamos consultar el registro completo en Laravel
        // Firebird solo entrega los cambios parciales en el log, no la fila entera.
        // Por eso se hace consulta "refetch" desde la tabla importada FULL.

        $row = $this->fetchFromFullSnapshot($table, $pk);

        if (!$row) {
            Log::warning("No se encontró registro completo luego de INSERT en tabla $table con PK=$pk");
            return;
        }

        $this->dispatchToPersister($table, $row);
    }

    /**
     * 2. UPDATE
     */
    private function applyUpdate(string $table, IncrementalLogDTO $log): void
    {
        $pk = $this->resolvePrimaryKey($log);

        // Pedimos al “full snapshot” el estado actual de la fila
        $row = $this->fetchFromFullSnapshot($table, $pk);

        if (!$row) {
            Log::warning("No existe el registro actualizado en $table con PK=$pk. Se ignora.");
            return;
        }

        $this->dispatchToPersister($table, $row);
    }

    /**
     * 3. DELETE
     * No borramos datos en SUAN, solo marcamos estado.
     */
    private function applyDelete(string $table, IncrementalLogDTO $log): void
    {
        $pk = $this->resolvePrimaryKey($log);

        if ($table === 'LEGAJOS') {
            $this->personPersister->save([
                'legacy_legajo_id' => $pk,
                'is_active' => false,
            ]);
            return;
        }

        if ($table === 'PLA_AGENTES') {
            $this->agentPersister->save([
                'legacy_legajo_id' => $pk,
                'status' => 'INACTIVO',
            ]);
        }
    }

    /**
     * Determina la PK que usa SUAN.
     */
    private function resolvePrimaryKey(IncrementalLogDTO $log): ?string
    {
        // Normalmente PK1 es LEGAJO
        return $log->pk1 ?: $log->pk2 ?: $log->pk3;
    }

    /**
     * PARA INSERT/UPDATE:
     * Obtenemos la fila completa desde el snapshot FULL.
     * Esto permite soportar Firebird → JSON hoy,
     * y Firebird → SQL directo mañana sin cambiar nada.
     */
    private function fetchFromFullSnapshot(string $table, string $pk): ?array
    {
        $path = storage_path("firebird_full/{$table}.json");

        if (!file_exists($path)) {
            Log::error("No existe snapshot FULL para tabla $table.");
            return null;
        }

        $data = json_decode(file_get_contents($path), true);

        foreach ($data['rows'] as $row) {
            if ((string)$row['LEGAJO'] === (string)$pk) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Envía la fila mapeada a su persister correspondiente.
     */
    private function dispatchToPersister(string $table, array $row): void
    {
        switch ($table) {
            case 'LEGAJOS':
                $mapped = $this->legajoMapper->map($row);
                $this->personPersister->save($mapped);
                break;

            case 'DAT_LABORALES_PLA':
                $mapped = $this->laborMapper->map($row);
                $this->laborPersister->save($mapped);
                break;

            case 'PLA_AGENTES':
                $mapped = $this->agentMapper->map($row);
                $this->agentPersister->save($mapped);
                break;

            default:
                Log::warning("Tabla incremental no manejada: $table");
        }
    }
}
