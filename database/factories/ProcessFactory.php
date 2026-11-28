<?php

namespace Database\Factories;

use App\Models\Boundary;
use App\Models\Process;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Process>
 */
class ProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'boundary_id' => Boundary::factory(),
            'description' => fake()->words(3, true),
            'sort_order' => 0,
        ];
    }
}
