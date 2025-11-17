<?php

namespace Database\Factories;

use App\Domain\Attendance\Models\SuanLaborLink;
use App\Domain\Attendance\Models\SuanPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanLaborLinkFactory extends Factory
{
    protected $model = SuanLaborLink::class;

    public function definition(): array
    {
        return [
            'person_id' => SuanPerson::factory(),
            'source' => $this->faker->word,
            'external_id' => $this->faker->uuid,
            'active' => $this->faker->boolean,
            'area' => $this->faker->word,
            'position' => $this->faker->jobTitle,
            'schedule' => ['start' => '09:00', 'end' => '17:00'],
        ];
    }
}