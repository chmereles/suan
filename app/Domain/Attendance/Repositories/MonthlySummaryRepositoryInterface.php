<?php

namespace App\Domain\Attendance\Repositories;

interface MonthlySummaryRepositoryInterface
{
    public function storeOrUpdate(int $laborLinkId, string $period, array $data);

    public function getByPeriod(string $period): array;
}
