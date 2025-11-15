<?php

namespace App\Domain\Attendance\Services;

use Carbon\Carbon;

class CrossChexMapper
{
    public function mapRecords(array $records): array
    {
        return collect($records)->map(function ($rec) {

            return [
                // ID del empleado según CrossChex
                'device_user_id' => $rec['employee']['workno'] ?? null,

                // N° de serie del dispositivo
                'device_serial'  => $rec['device']['serial_number'] ?? null,

                // Tipo de marcación
                'record_type'    => $rec['checktype'] ?? null,

                // Timestamp exacto de fichada
                'recorded_at'    => Carbon::parse($rec['checktime'])
                    ->timezone(config('app.timezone'))
                    ->toDateTimeString(),

                // ID único del registro
                'raw_id'         => $rec['uuid'] ?? null,

                // Registro completo para auditoría
                // 'raw_payload'    => $rec,
                'raw_payload' => json_encode($rec),
            ];
        })->toArray();
    }
}
