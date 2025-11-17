<?php

namespace Database\Factories;

use App\Domain\Attendance\Models\SuanPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuanPersonFactory extends Factory
{
    protected $model = SuanPerson::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->uuid,
            'document' => $this->faker->unique()->numerify('########'),
            'full_name' => $this->faker->name,
        ];
    }
}