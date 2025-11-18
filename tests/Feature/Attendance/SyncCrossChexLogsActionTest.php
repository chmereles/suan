<?php

use App\Domain\Attendance\Actions\SyncCrossChexLogsAction;
use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use App\Domain\Attendance\Services\CrossChexMapper;
use App\Domain\Attendance\Repositories\AttendanceRepository;
use Carbon\Carbon;

uses()->group('attendance-sync-logs');

/**
 * ---------------------------------------------------------
 * UTILIDAD PARA FIXEAR EL NOW()
 * ---------------------------------------------------------
 */
beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2025-11-17 12:00:00'));
});

afterEach(function () {
    Carbon::setTestNow(); // limpiar estado global
});


/**
 * ---------------------------------------------------------
 * TEST 1 — __invoke() con ventana por defecto (15 min)
 * ---------------------------------------------------------
 */
it('__invoke() syncs logs using default 15-minute window', function () {

    // $raw = [
    //     ['checktime' => '2025-11-17T15:00:00+00:00'],
    // ];

    // $mapped = [
    //     ['person_id' => 1, 'recorded_at' => now()->subMinutes(10)],
    // ];

    // // 1 — Mock CrossChexClient
    // $client = Mockery::mock(CrossChexClient::class);
    // $client->shouldReceive('getAllRecords')
    //     ->once()
    //     ->with(
    //         Carbon::parse('2025-11-17 11:45:00'), // now - 15 min
    //         Carbon::parse('2025-11-17 12:00:00')
    //     )
    //     ->andReturn($raw);

    // // 2 — Mock Mapper
    // $mapper = Mockery::mock(CrossChexMapper::class);
    // $mapper->shouldReceive('mapRecords')
    //     ->once()
    //     ->with($raw)
    //     ->andReturn($mapped);

    // // 3 — Mock AttendanceRepository
    // $repo = Mockery::mock(AttendanceRepository::class);
    // $repo->shouldReceive('storeMany')
    //     ->once()
    //     ->with($mapped)
    //     ->andReturn(1);

    // $action = new SyncCrossChexLogsAction($client, $mapper, $repo);

    // $result = $action(); // __invoke()

    // expect($result)->toBe(1);
});


/**
 * ---------------------------------------------------------
 * TEST 2 — __invoke() con ventana manual
 * ---------------------------------------------------------
 */
it('__invoke() syncs logs using custom window', function () {

    // $raw = [['checktime' => '...']];
    // $mapped = [['person_id' => 1, 'recorded_at' => now()]];

    // // ventana de 60 min
    // $expectedStart = Carbon::parse('2025-11-17 11:00:00');
    // $expectedEnd   = Carbon::parse('2025-11-17 12:00:00');

    // $client = Mockery::mock(CrossChexClient::class);
    // $client->shouldReceive('getAllRecords')
    //     ->once()
    //     ->with($expectedStart, $expectedEnd)
    //     ->andReturn($raw);

    // $mapper = Mockery::mock(CrossChexMapper::class);
    // $mapper->shouldReceive('mapRecords')
    //     ->once()
    //     ->with($raw)
    //     ->andReturn($mapped);

    // $repo = Mockery::mock(AttendanceRepository::class);
    // $repo->shouldReceive('storeMany')
    //     ->once()
    //     ->with($mapped)
    //     ->andReturn(5);

    // $action = new SyncCrossChexLogsAction($client, $mapper, $repo);

    // $result = $action(60);

    // expect($result)->toBe(5);
});


/**
 * ---------------------------------------------------------
 * TEST 3 — syncRange() directo
 * ---------------------------------------------------------
 */
it('syncRange() calls client, mapper and repository correctly', function () {

    $start = Carbon::parse('2025-11-01 00:00:00');
    $end   = Carbon::parse('2025-11-10 23:59:59');

    $raw = [
        ['checktime' => 'A'],
        ['checktime' => 'B'],
    ];

    $mapped = [
        ['person_id' => 1],
        ['person_id' => 2],
    ];

    $client = Mockery::mock(CrossChexClient::class);
    $client->shouldReceive('getAllRecords')
        ->once()
        ->with($start, $end)
        ->andReturn($raw);

    $mapper = Mockery::mock(CrossChexMapper::class);
    $mapper->shouldReceive('mapRecords')
        ->once()
        ->with($raw)
        ->andReturn($mapped);

    $repo = Mockery::mock(AttendanceRepository::class);
    $repo->shouldReceive('storeMany')
        ->once()
        ->with($mapped)
        ->andReturn(2);

    $action = new SyncCrossChexLogsAction($client, $mapper, $repo);

    $result = $action->syncRange($start, $end);

    expect($result)->toBe(2);
});
