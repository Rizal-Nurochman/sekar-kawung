<?php

namespace Database\Factories;

use App\Models\EnergySource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EnergySource>
 */
class EnergySourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'description' => fake()->optional(0.7)->sentence(),
            'slug' => Str::slug($name),
            'emission_multiplier' => fake()->randomFloat(3, 0.1, 2),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
