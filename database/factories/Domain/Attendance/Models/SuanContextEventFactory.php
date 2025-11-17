<?php

namespace Database\Factories\Domain\Attendance\Models;

use App\Domain\Attendance\Models\SuanContextEvent;
use App\Domain\Attendance\Models\SuanLaborLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanContextEventFactory extends Factory
{
    protected $model = SuanContextEvent::class;

    public function definition(): array
    {
        return [
            'labor_link_id' => SuanLaborLink::factory(),
            'date' => fake()->date(),
            'type_id' => 1,
            'source' => 'manual',
            'description' => fake()->word(),
        ];
    }
}