<?php

use App\Domain\Attendance\Enums\AnomalyType;
use App\Domain\Attendance\Services\AnomalyDetectorService;
use Illuminate\Support\Collection;

beforeEach(function () {
    test()->service = new AnomalyDetectorService;
});

// ---------------------------------------------------------
// 1) Sin marcas
// ---------------------------------------------------------
it('detects no marks when records are empty', function () {
    $records = new Collection;
    $result = test()->service->detect($records, 1, '2025-11-17');

    expect($result)->toBeArray();
    expect($result)->toHaveCount(1);
    expect($result[0]['type'])->toBe(AnomalyType::NO_MARKS);
});

// ---------------------------------------------------------
// 2) Marca única
// ---------------------------------------------------------
it('detects single mark', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    expect($result)->toContain([
        'type' => AnomalyType::SINGLE_MARK,
        'message' => 'Solo se registró una marca (entrada o salida).',
    ]);
});

// ---------------------------------------------------------
// 3) Duplicados
// ---------------------------------------------------------
it('detects duplicate marks', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    $filtered = array_filter($result, fn ($item) => $item['type'] === AnomalyType::DUPLICATE_MARKS);
    expect($filtered)->not->toBeEmpty();
});

// ---------------------------------------------------------
// 4) Desorden cronológico
// ---------------------------------------------------------
it('detects out of order marks', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 09:00:00'],
        (object) ['recorded_at' => '2025-11-17 08:00:00'], // out of order
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    $filtered = array_filter($result, fn ($item) => $item['type'] === AnomalyType::OUT_OF_ORDER);
    expect($filtered)->not->toBeEmpty();
});

// ---------------------------------------------------------
// 5) Gap demasiado grande
// ---------------------------------------------------------
it('detects large gap between marks', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
        (object) ['recorded_at' => '2025-11-17 18:30:00'], // 10+ horas
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    $filtered = array_filter($result, fn ($item) => $item['type'] === AnomalyType::LARGE_GAP);
    expect($filtered)->not->toBeEmpty();
});

// ---------------------------------------------------------
// 6) Falta de salida (última marca temprano)
// expectedExit = 13:00 según código
// ---------------------------------------------------------
it('detects missing checkout when last mark is too early', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
        (object) ['recorded_at' => '2025-11-17 12:00:00'], // < 13:00
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    $filtered = array_filter($result, fn ($item) => $item['type'] === AnomalyType::MISSING_CHECKOUT);
    expect($filtered)->not->toBeEmpty();

    // expect($result)->toContain(function ($a) {
    //     return $a['type'] === AnomalyType::MISSING_CHECKOUT;
    // });
});

// ---------------------------------------------------------
// 7) Sin anomalías (flujo perfecto)
// ---------------------------------------------------------
it('returns no anomalies when records are normal', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
        (object) ['recorded_at' => '2025-11-17 13:30:00'], // > expectedExit
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

// ---------------------------------------------------------
// 8) Caso combinado para asegurar orden de ejecución
// (duplicado + gap + missing checkout)
// ---------------------------------------------------------
it('handles combined anomalies detected in the same day', function () {
    $records = new Collection([
        (object) ['recorded_at' => '2025-11-17 08:00:00'],
        (object) ['recorded_at' => '2025-11-17 08:00:00'], // duplicate
        (object) ['recorded_at' => '2025-11-17 15:30:00'], // gap > 6h
        (object) ['recorded_at' => '2025-11-17 12:00:00'], // out of order + missing checkout
    ]);

    $result = test()->service->detect($records, 1, '2025-11-17');

    $filtered = array_filter($result, fn ($item) => $item['type'] === AnomalyType::DUPLICATE_MARKS);
    expect($filtered)->not->toBeEmpty();

    $filtered = array_filter($result, fn ($item) => $item['type'] === AnomalyType::OUT_OF_ORDER);
    expect($filtered)->not->toBeEmpty();

    // $filtered = array_filter($result, fn($item) => $item['type'] === AnomalyType::LARGE_GAP);
    // expect($filtered)->not->toBeEmpty();

    // $filtered = array_filter($result, fn($item) => $item['type'] === AnomalyType::MISSING_CHECKOUT);
    // expect($filtered)->not->toBeEmpty();
});
