<?php

use App\Infrastructure\Attendance\Persistence\EloquentAttendanceRecordRepository;
use App\Domain\Attendance\DTO\ProcessedRecordDTO;
use App\Domain\Attendance\Models\SuanLaborLink;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('stores and retrieves attendance records by labor link', function () {
        $link = SuanLaborLink::factory()->create();

        $repo = app(EloquentAttendanceRecordRepository::class);

        $dto = new ProcessedRecordDTO(
                laborLinkId: $link->id,
                date: '2025-01-10',
                type: 'morning',
                recordedAt: '2025-01-10 08:00:00',
                attendanceLogId: 1,
                rawId: 'RAW123',
                rawPayload: ['test' => true],
                metadata: ['source' => 'test']
        );

        $repo->store($dto);

        $records = $repo->getByLaborLinkAndDate($link->id, '2025-01-10');

        expect($records)->toHaveCount(1);
        expect($records[0]->labor_link_id)->toBe($link->id);
        expect($records[0]->type)->toBe('morning');
});
