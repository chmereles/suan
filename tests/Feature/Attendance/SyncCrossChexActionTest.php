<?php

use App\Domain\Attendance\Actions\SyncCrossChexAction;
use App\Domain\Attendance\Services\CrossChexMapper;
use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use App\Domain\Attendance\Repositories\AttendanceRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

uses()->group('attendance-sync');

beforeEach(function () {
    // Garantizar que la tabla exista y esté limpia
    DB::table('attendance_sync_logs')->truncate();
});

/**
 * ---------------------------------------------------------
 *  TEST 1 — Flujo normal
 * ---------------------------------------------------------
 */
it('syncs CrossChex records successfully', function () {

    // Datos fake de CrossChex
    $raw = [
        ['checktime' => '2025-11-17T16:03:08+00:00'],
        ['checktime' => '2025-11-17T16:10:05+00:00'],
    ];

    $mapped = [
        ['person_id' => 1, 'recorded_at' => now(), 'raw_payload' => $raw[0]],
        ['person_id' => 1, 'recorded_at' => now(), 'raw_payload' => $raw[1]],
    ];

    // Mock del cliente
    $client = Mockery::mock(CrossChexClient::class);
    $client->shouldReceive('getAllRecords')
        ->once()
        ->andReturn($raw);

    // Mock del mapper
    $mapper = Mockery::mock(CrossChexMapper::class);
    $mapper->shouldReceive('mapRecords')
        ->once()
        ->with($raw)
        ->andReturn($mapped);

    // Mock del repositorio
    $repo = Mockery::mock(AttendanceRepository::class);
    $repo->shouldReceive('storeMany')
        ->once()
        ->with($mapped)
        ->andReturn(2);

    // Crear Action
    $action = new SyncCrossChexAction($client, $mapper, $repo);

    $start = Carbon::parse('2025-11-17 00:00:00');
    $end   = Carbon::parse('2025-11-17 23:59:59');

    $result = $action->execute($start, $end);

    // Assert del response
    expect($result)->toBe([
        'status' => 'ok',
        'inserted' => 2,
    ]);

    // Assert del log de sincronización
    $log = DB::table('attendance_sync_logs')->first();

    expect($log)->not->toBeNull();
    expect($log->status)->toBe('ok');
    expect($log->inserted_count)->toBe(2);
    expect($log->error_message)->toBeNull();
});

/**
 * ---------------------------------------------------------
 *  TEST 2 — Flujo con ERROR
 * ---------------------------------------------------------
 */
it('logs error if exception occurs', function () {

    $client = Mockery::mock(CrossChexClient::class);
    $mapper = Mockery::mock(CrossChexMapper::class);

    // Hacer que el cliente falle
    $client->shouldReceive('getAllRecords')
        ->once()
        ->andThrow(new Exception('Falla CrossChex'));

    // El repo no debe ejecutarse nunca
    $repo = Mockery::mock(AttendanceRepository::class);
    $repo->shouldNotReceive('storeMany');

    $action = new SyncCrossChexAction($client, $mapper, $repo);

    $start = Carbon::parse('2025-11-17 00:00:00');
    $end   = Carbon::parse('2025-11-17 23:59:59');

    try {
        $action->execute($start, $end);
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toContain('Falla CrossChex');
    }

    // Verificar que el log quedó guardado con error
    $log = DB::table('attendance_sync_logs')->first();

    expect($log)->not->toBeNull();
    expect($log->status)->toBe('error');
    expect($log->error_message)->toContain('Falla CrossChex');
});
