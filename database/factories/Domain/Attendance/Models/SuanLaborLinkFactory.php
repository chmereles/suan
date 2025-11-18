<?php

namespace Database\Factories\Domain\Attendance\Models;

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Models\SuanPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanLaborLinkFactory extends Factory
{
    protected $model = SuanLaborLink::class;

    public function definition(): array
    {
        $data = [
            'person_id' => SuanPerson::factory(),
            'source' => fake()->randomElement(['haberes', 'planes']),
            'external_id' => fake()->uuid,
            'active' => fake()->boolean,
            'area' => fake()->word,
            'position' => fake()->jobTitle,
            'schedule' => ['start' => '09:00', 'end' => '17:00'],
        ];

        return $data;
    }
}
