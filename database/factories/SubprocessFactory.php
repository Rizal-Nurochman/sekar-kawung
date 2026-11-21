<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\Subprocess;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subprocess>
 */
class SubprocessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'process_id' => Process::factory(),
            'description' => fake()->words(3, true),
            'sort_order' => 0,
        ];
    }
}
