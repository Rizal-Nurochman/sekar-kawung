<?php

namespace Database\Factories;

use App\Models\EmissionFactor;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmissionFactor>
 */
class EmissionFactorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_id' => Source::factory(),
            'unit_name' => fake()->randomElement(['kg CO2e/kWh', 'kg CO2/km', 'kg N2O-N']),
            'ef_value' => fake()->randomFloat(10, 0, 10),
        ];
    }

    public function withoutSource(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_id' => null,
        ]);
    }
}
