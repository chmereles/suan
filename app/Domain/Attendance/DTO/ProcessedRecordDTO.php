<?php

namespace App\Domain\Attendance\DTO;

/**
 * Representa un registro de asistencia procesado y listo
 * para persistirse en suan_attendance_records.
 */
class ProcessedRecordDTO
{
    /**
     * @param  int         $laborLinkId       ID de la relación laboral (suan_labor_links.id)
     * @param  string      $date              Día normalizado (YYYY-MM-DD)
     * @param  string|null $type              Tipo inferido (in, out, unknown)
     * @param  string      $recordedAt        Timestamp procesado (Y-m-d H:i:s)
     * @param  int|null    $attendanceLogId   ID del log crudo (attendance_logs.id)
     * @param  string      $source            Origen de la marca (device, manual, import, auto)
     * @param  array       $metadata          Información ligera adicional de procesamiento
     */
    public function __construct(
        public int $laborLinkId,
        public string $date,
        public ?string $type,
        public string $recordedAt,
        public ?int $attendanceLogId = null,
        public string $source = 'device',
        public array $metadata = [],
    ) {}
}
