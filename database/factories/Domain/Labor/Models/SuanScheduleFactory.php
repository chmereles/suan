<?php

namespace Database\Factories\Domain\Labor\Models;

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Labor\Models\SuanSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanScheduleFactory extends Factory
{
    protected $model = SuanSchedule::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->randomElement(['fixed']),
            'active' => 1,
        ];
    }
}
