<?php

namespace App\Domain\FirebirdSync\Services;

use App\Domain\FirebirdSync\DTO\IncrementalLogDTO;
use App\Domain\FirebirdSync\DTO\ChangeDTO;
use App\Domain\Attendance\Models\SuanPerson;
use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Support\Facades\Log;

class IncrementalSyncService
{
    /**
     * Column mapping por tabla Firebird → atributo SUAN.
     * Solo incluimos las columnas que realmente nos interesa sincronizar.
     */
    private array $mapLegajos = [
        'NUM_DOCU'       => 'document',
        // 'FEC_NACIMIENTO' => 'birth_date',
        // 'CUIL'           => 'cuil',
        // 'NOMBRE'         => 'first_name',
        // 'APELLIDO'       => 'last_name',
        // 'EMAIL'          => 'email',
        // 'SEXO'           => 'sex',
        // 'EST_CIVIL'      => 'civil_status',
        // 'PAGO'        => 'payment_method', // si luego tenés el campo en suan_people
    ];

    private array $mapLaborales = [
        'REFDEPART'      => 'area',
        'ACTIVO'         => 'active',
        // 'FUNCION'        => 'position',
        // si luego agregás más campos en suan_labor_links:
        // 'COD_CATEGORIA'  => 'category',
        // 'FEC_BAJA'       => 'end_date',
    ];

    private array $mapPlanes = [
        'AG_DEPENDENCIA_ID' => 'area',
        'AG_ACTIVO'         => 'active',
        // 'TIPO'           => 'plan_type', // si luego lo agregás a suan_labor_links
    ];

    public function apply(IncrementalLogDTO $log): void
    {
        try {
            $table = strtoupper($log->table);

            match ($table) {
                'LEGAJOS'       => $this->applyForLegajos($log),
                'DAT_LABORALES' => $this->applyForDatLaborales($log),
                'PLA_AGENTES'   => $this->applyForPlaAgentes($log),
                default         => Log::warning("Tabla Firebird no manejada en incremental: {$log->table}")
            };
        } catch (\Throwable $e) {
            Log::error("Error procesando incremental ID {$log->id}: {$e->getMessage()}", [
                'exception' => $e,
                'log_id'    => $log->id,
                'table'     => $log->table,
                'operation' => $log->operation,
            ]);
        }
    }

    /* ============================================================
     *  LEGAJOS → suan_people
     * ============================================================
     */
    private function applyForLegajos(IncrementalLogDTO $log): void
    {
        $legacyId = $log->pk1;

        if (!$legacyId) {
            Log::warning("Log LEGAJOS sin pk1 / LEGAJO. ID={$log->id}");
            return;
        }

        // Buscamos persona por legacy_legajo_id
        $person = SuanPerson::firstOrNew(['external_id' => $legacyId]);

        // Operaciones
        switch ($log->operation) {
            case 'I':
            case 'U':
                $this->applyChangesToModel($person, $log->changes, $this->mapLegajos, 'LEGAJOS');
                // Si es un insert y no tenía nada, al menos activamos
                if ($log->operation === 'I' && !$person->exists) {
                    if ($person->is_active === null) {
                        $person->is_active = true;
                    }
                }
                $person->save();
                break;

            case 'D':
                // No borramos físicamente, solo marcamos inactivo
                if ($person->exists) {
                    $person->is_active = false;
                    $person->save();
                }
                break;

            default:
                Log::warning("Operación LEGAJOS no manejada: {$log->operation}");
        }
    }

    /* ============================================================
     *  DAT_LABORALES → suan_labor_links (source = haberes)
     *
     *  pk1 = LEGAJO (PK del registro laboral, NO el del empleado)
     *  suan_labor_links.external_id = pk1
     * ============================================================
     */
    private function applyForDatLaborales(IncrementalLogDTO $log): void
    {
        $externalId = $log->pk1;

        if (!$externalId) {
            Log::warning("Log DAT_LABORALES sin pk1. ID={$log->id}");
            return;
        }

        switch ($log->operation) {
            case 'I':
                // Según tu aclaración: los inserts se resuelven volviendo a importar FULL.
                Log::info("INSERT en DAT_LABORALES ignorado en incremental. ID={$log->id}, PK={$externalId}");
                return;

            case 'U':
                $link = SuanLaborLink::where('external_id', $externalId)
                    ->where('source', 'haberes')
                    ->first();

                if (!$link) {
                    Log::warning("No se encontró labor_link(haberes) para external_id={$externalId} en UPDATE. LogID={$log->id}");
                    return;
                }

                $this->applyChangesToModel($link, $log->changes, $this->mapLaborales, 'DAT_LABORALES');
                $link->save();
                break;

            case 'D':
                $link = SuanLaborLink::where('external_id', $externalId)
                    ->where('source', 'haberes')
                    ->first();

                if ($link) {
                    $link->active = false;
                    $link->save();
                }
                break;

            default:
                Log::warning("Operación DAT_LABORALES no manejada: {$log->operation}");
        }
    }

    /* ============================================================
     *  PLA_AGENTES → suan_labor_links (source = planes)
     *
     *  pk1 = AG_DOC_NUM (DNI)
     *  suan_labor_links.external_id = AG_DOC_NUM
     *  el person se vincula por document (dni)
     * ============================================================
     */
    private function applyForPlaAgentes(IncrementalLogDTO $log): void
    {
        $docNum = $log->pk1;

        if (!$docNum) {
            Log::warning("Log PLA_AGENTES sin pk1 / AG_DOC_NUM. ID={$log->id}");
            return;
        }

        switch ($log->operation) {
            case 'I':
                // 1) Buscar o crear persona por documento
                $person = SuanPerson::firstOrCreate(
                    ['document' => $docNum],
                    [
                        // campos mínimos por defecto
                        'is_active' => true,
                    ]
                );

                // 2) Buscar o crear vínculo de planes
                $link = SuanLaborLink::firstOrNew([
                    'person_id'   => $person->id,
                    'source'      => 'planes',
                    'external_id' => $docNum,
                ]);

                // 3) Aplicar cambios
                $this->applyChangesToModel($link, $log->changes, $this->mapPlanes, 'PLA_AGENTES');

                if (!$link->exists) {
                    // Si no tenía valor de active, asumimos que viene activo
                    if ($link->active === null) {
                        $link->active = true;
                    }
                }

                $link->save();
                break;

            case 'U':
                $link = SuanLaborLink::where('external_id', $docNum)
                    ->where('source', 'planes')
                    ->first();

                if (!$link) {
                    Log::warning("No se encontró labor_link(planes) para AG_DOC_NUM={$docNum} en UPDATE. LogID={$log->id}");
                    return;
                }

                $this->applyChangesToModel($link, $log->changes, $this->mapPlanes, 'PLA_AGENTES');
                $link->save();
                break;

            case 'D':
                $link = SuanLaborLink::where('external_id', $docNum)
                    ->where('source', 'planes')
                    ->first();

                if ($link) {
                    $link->active = false;
                    $link->save();
                }
                break;

            default:
                Log::warning("Operación PLA_AGENTES no manejada: {$log->operation}");
        }
    }

    /* ============================================================
     *  Helper: aplicar cambios de una lista de ChangeDTO a un modelo
     * ============================================================
     */
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  ChangeDTO[]                          $changes
     * @param  array                                $columnMap  [firebird_column => suan_attribute]
     * @param  string                               $tableName  solo para logs
     */
    private function applyChangesToModel($model, array $changes, array $columnMap, string $tableName): void
    {
        foreach ($changes as $change) {
            $column = $change->column;

            if (!isset($columnMap[$column])) {
                // columna que nos llega en el log pero que aún no mapeamos
                Log::info("Columna {$tableName}.{$column} sin mapping; se ignora. Log incremental.");
                continue;
            }

            $attribute = $columnMap[$column];
            $newValue  = $this->normalizeValue($tableName, $column, $change->new);

            $model->$attribute = $newValue;
        }
    }

    /* ============================================================
     *  Normalización de valores Firebird → tipos SUAN
     * ============================================================
     */
    private function normalizeValue(string $table, string $column, $value): mixed
    {
        if ($value === null) {
            return null;
        }

        // Fechas (vienen como 'YYYY-MM-DD' desde Python safe_json)
        if (in_array($column, ['FEC_NACIMIENTO', 'FEC_ALTA', 'FEC_ALTA_AREA', 'FEC_BAJA', 'AG_FEC_ALTA', 'AG_FEC_BAJA'], true)) {
            return $value; // Laravel cast a date si el modelo lo define
        }

        // Booleanos S/N
        if (in_array($column, ['ACTIVO', 'BLOQUEADO', 'AG_ACTIVO'], true)) {
            return strtoupper((string) $value) === 'S';
        }

        // Podés agregar aquí normalización de códigos, enums, etc.
        // if ($table === 'LEGAJOS' && $column === 'EST_CIVIL') { ... }

        return $value;
    }
}
