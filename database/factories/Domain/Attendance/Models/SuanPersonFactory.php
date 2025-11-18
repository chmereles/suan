<?php

namespace Database\Factories\Domain\Attendance\Models;

use App\Domain\Attendance\Models\SuanPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanPersonFactory extends Factory
{
    protected $model = SuanPerson::class;

    public function definition(): array
    {
        return [
            'external_id' => fake()->uuid,
            'document' => fake()->unique()->numerify('########'),
            'full_name' => fake()->name,
        ];
    }
}
