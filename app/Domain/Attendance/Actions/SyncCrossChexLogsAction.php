<?php 
// app/Domain/Attendance/Actions/SyncCrossChexLogsAction.php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Models\CrossChexLog;
use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use Illuminate\Support\Facades\DB;

class SyncCrossChexLogsAction
{
    public function __construct(
        private readonly CrossChexClient $client,
    ) {
    }

    public function __invoke(?int $windowMinutes = null): int
    {
        $windowMinutes ??= (int) config('crosschex.sync_window_minutes', 60);

        $end   = now();
        $begin = $end->clone()->subMinutes($windowMinutes);

        $token   = $this->client->getToken();
        $records = $this->client->getRecords(
            token: $token,
            beginIso: $begin->toIso8601String(),
            endIso: $end->toIso8601String(),
        );

        $inserted = 0;

        DB::transaction(function () use ($records, &$inserted) {
            foreach ($records as $record) {
                $uuid = $record['uuid'] ?? null;

                if (! $uuid) {
                    continue;
                }

                $created = CrossChexLog::firstOrCreate(
                    ['uuid' => $uuid],
                    [
                        'employee_workno' => $record['employee']['workno'] ?? null,
                        'checktype'       => $record['checktype'] ?? null,
                        'checktime'       => $record['checktime'] ?? null,
                        'device_serial'   => $record['device']['serial_number'] ?? null,
                        'raw'             => $record,
                    ]
                );

                if ($created->wasRecentlyCreated) {
                    $inserted++;
                }
            }
        });

        return $inserted;
    }
}
