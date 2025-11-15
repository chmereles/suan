<?php

namespace App\Domain\Attendance\DTO;

/**
 * Representa un registro de asistencia ya procesado y listo
 * para ser persistido en la tabla suan_attendance_records.
 */
class ProcessedRecordDTO
{
    /**
     * @param  int         $employeeId       ID interno del empleado (suan_employees.id)
     * @param  string      $date             Fecha normalizada (YYYY-MM-DD)
     * @param  string|null $type             Segmento/Tipo inferido (ej: 'morning', 'afternoon', 'extra', etc.)
     * @param  string      $recordedAt       Timestamp completo de la marcación (Y-m-d H:i:s)
     * @param  int|null    $attendanceLogId  ID del registro crudo en attendance_logs (opcional)
     * @param  string|null $rawId            Identificador crudo del dispositivo (raw_id de CrossChex)
     * @param  array       $rawPayload       Payload original del log (para auditoría)
     * @param  array       $metadata         Info adicional de procesamiento (orden, source, etc.)
     */
    public function __construct(
        public int $employeeId,
        public string $date,
        public ?string $type,
        public string $recordedAt,
        public ?int $attendanceLogId = null,
        public ?string $rawId = null,
        public array $rawPayload = [],
        public array $metadata = [],
    ) {}
}
