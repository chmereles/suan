<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanContextEvent;
use Carbon\CarbonInterface;

interface ContextEventRepositoryInterface
{
    public function store(array $data): SuanContextEvent;

    public function hasEventForDate(int $laborLinkId, CarbonInterface $date): bool;

    public function getForLaborLinkAndDate(int $laborLinkId, CarbonInterface $date): array;
}
