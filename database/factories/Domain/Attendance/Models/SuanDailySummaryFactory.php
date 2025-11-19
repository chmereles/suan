<?php

namespace Database\Factories\Domain\Attendance\Models;

use App\Domain\Attendance\Models\SuanDailySummary;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanDailySummaryFactory extends Factory
{
    protected $model = SuanDailySummary::class;

    public function definition(): array
    {
        return [
            'labor_link_id' => fake()->uuid,
            'date' => now(),
        ];
    }
}
