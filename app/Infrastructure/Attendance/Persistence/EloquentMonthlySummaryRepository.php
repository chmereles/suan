<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanMonthlySummary;
use App\Domain\Attendance\Repositories\MonthlySummaryRepositoryInterface;

class EloquentMonthlySummaryRepository implements MonthlySummaryRepositoryInterface
{
    public function storeOrUpdate(int $laborLinkId, string $period, array $data)
    {
        return SuanMonthlySummary::updateOrCreate(
            [
                'labor_link_id' => $laborLinkId,
                'period' => $period,
            ],
            $data
        );
    }

    public function getByPeriod(string $period): array
    {
        return SuanMonthlySummary::where('period', $period)
            ->get()
            ->toArray();
    }
}
