<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DailySummaryController
{
    public function __construct(
        private DailySummaryRepositoryInterface $summaryRepo,
    ) {}

    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $summaries = $this->summaryRepo->getByDate($date);

        return Inertia::render('Attendance/Dashboard', [
            'date' => $date,
            'summaries' => $summaries,
        ]);
    }
}
