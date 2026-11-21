<?php

namespace Database\Factories;

use App\Models\ActivityUnit;
use App\Models\EmissionFactor;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity_unit_id' => ActivityUnit::factory(),
            'emission_factor_id' => EmissionFactor::factory(),
            'description' => fake()->words(2, true),
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
