<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanDailySummary;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;

class EloquentDailySummaryRepository implements DailySummaryRepositoryInterface
{
    public function findByLaborLinkAndDate(int $laborLinkId, string $date): ?SuanDailySummary
    {
        return SuanDailySummary::where('labor_link_id', $laborLinkId)
            ->where('date', $date)
            ->first();
    }

    public function storeOrUpdate(int $laborLinkId, string $date, array $data): SuanDailySummary
    {
        return SuanDailySummary::updateOrCreate(
            [
                'labor_link_id' => $laborLinkId,
                'date' => $date,
            ],
            $data
        );
    }

    public function getByDate(string $date): array
    {
        return SuanDailySummary::query()
            ->with([
                'laborLink:id,person_id', // Traemos el labor_link con su person_id
                'laborLink.person:id,document,full_name,device_user_id', // Traemos los datos de person
            ])
            ->where('date', $date)
            ->orderBy('labor_link_id')
            ->get()
            ->toArray();
    }
}
