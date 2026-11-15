<?php

namespace Database\Factories;

use App\Models\CarbonFootprint;
use App\Models\Textile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Textile>
 */
class TextileFactory extends Factory
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
            'slug' => Str::slug($name),
            'is_calculable' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * Every textile carries exactly one footprint; this makes it explicit.
     */
    public function withFootprint(): static
    {
        return $this->has(CarbonFootprint::factory());
    }

    public function uncalculable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_calculable' => false,
        ]);
    }
}
