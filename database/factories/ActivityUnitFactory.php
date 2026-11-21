<?php

namespace Database\Factories;

use App\Models\ActivityUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityUnit>
 */
class ActivityUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_name' => fake()->unique()->words(2, true),
        ];
    }
}
