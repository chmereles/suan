<?php

use App\Domain\Attendance\Services\AttendanceRecordProcessor;
use Illuminate\Support\Collection;

it('it_returns_empty_array_when_logs_are_empty', function () {
    $processor = new AttendanceRecordProcessor();
    $logs = new Collection(); // Colección vacía
    $laborLinkId = 1;
    $date = '2025-11-17';

    // Act
    $result = $processor->processLaborLinkLogs($logs, $laborLinkId, $date);

    // Assert
    $this->assertIsArray($result);
    $this->assertEmpty($result);
});
