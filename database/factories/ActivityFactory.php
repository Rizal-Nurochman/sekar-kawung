<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityUnit;
use App\Models\EmissionFactor;
use App\Models\Subprocess;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subprocess_id' => Subprocess::factory(),
            'activity_unit_id' => ActivityUnit::factory(),
            'emission_factor_id' => EmissionFactor::factory(),
            'sort_order' => 0,
            'description' => fake()->words(3, true),
        ];
    }

    public function withoutUnit(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_unit_id' => null,
        ]);
    }

    public function withoutEmissionFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'emission_factor_id' => null,
        ]);
    }
}
