<?php

namespace Database\Factories;

use App\Enums\DataStatus;
use App\Enums\Recyclability;
use App\Models\CarbonFootprint;
use App\Models\Textile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarbonFootprint>
 */
class CarbonFootprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'textile_id' => Textile::factory(),
            'kg_co2e_per_kg' => fake()->randomFloat(4, 0.5, 12),
            'system_boundary' => 'cradle_to_gate',
            'data_status' => DataStatus::Placeholder,
            'water_l_per_kg' => fake()->optional(0.5)->randomFloat(2, 5, 100),
            'sustainability_score' => fake()->optional(0.5)->randomFloat(1, 1, 5),
            'recyclability' => fake()->optional(0.5)->randomElement(Recyclability::cases()),
            'verified_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_status' => DataStatus::Verified,
            'verified_by' => fake()->name(),
            'verified_at' => now(),
        ]);
    }
}
