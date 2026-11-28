<?php

namespace Database\Factories;

use App\Models\Boundary;
use App\Models\Scheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Boundary>
 */
class BoundaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scheme_id' => Scheme::factory(),
            'description' => fake()->words(3, true),
            'sort_order' => 0,
        ];
    }
}
