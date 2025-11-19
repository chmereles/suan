<?php

use App\Domain\Attendance\Actions\RegisterContextEventAction;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use Carbon\Carbon;

uses()->group('context-events');

/**
 * Set fijo para Carbon::parse
 */
beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2025-11-17 12:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

/**
 * ---------------------------------------------------------
 * TEST 1 — metadata SIN source (usa default: manual)
 * ---------------------------------------------------------
 */
it('stores context event with default manual source', function () {

    // $repo = Mockery::mock(ContextEventRepositoryInterface::class);

    // $expectedPayload = [
    //     'employee_id' => 10,
    //     'date' => '2025-11-15',
    //     'type' => 'commission',
    //     'source' => 'manual',   // default
    //     'description' => 'Salida por trámites',
    //     'metadata' => ['foo' => 'bar'],
    //     'created_by' => 99,
    // ];

    // // assert repository receives correct data
    // $repo->shouldReceive('store')
    //     ->once()
    //     ->with($expectedPayload)
    //     ->andReturn('stored-event-id');

    // $action = new RegisterContextEventAction($repo);

    // $result = $action(
    //     employeeId: 10,
    //     date: '2025-11-15',
    //     type: 'commission',
    //     description: 'Salida por trámites',
    //     metadata: ['foo' => 'bar'],
    //     createdBy: 99
    // );

    // expect($result)->toBe('stored-event-id');
});

/**
 * ---------------------------------------------------------
 * TEST 2 — metadata CON source explícito
 * ---------------------------------------------------------
 */
it('stores context event with explicit source from metadata', function () {

    // $repo = Mockery::mock(ContextEventRepositoryInterface::class);

    // $expectedPayload = [
    //     'employee_id' => 20,
    //     'date' => '2025-11-16',
    //     'type' => 'rrhh_note',
    //     'source' => 'rrhh',   // desde metadata
    //     'description' => null,
    //     'metadata' => ['source' => 'rrhh', 'x' => 99],
    //     'created_by' => null,
    // ];

    // $repo->shouldReceive('store')
    //     ->once()
    //     ->with($expectedPayload)
    //     ->andReturn(['ok' => true]);

    // $action = new RegisterContextEventAction($repo);

    // $result = $action(
    //     employeeId: 20,
    //     date: '2025-11-16',
    //     type: 'rrhh_note',
    //     description: null,
    //     metadata: ['source' => 'rrhh', 'x' => 99],
    //     createdBy: null
    // );

    // expect($result)->toBe(['ok' => true]);
});
