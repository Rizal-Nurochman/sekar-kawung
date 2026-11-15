<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Textile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasMoq = fake()->boolean(70);

        return [
            'textile_id' => Textile::factory(),
            'description' => fake()->paragraph(),
            'image_path' => null,
            'moq_quantity' => $hasMoq ? fake()->randomFloat(2, 100, 5000) : null,
            'moq_unit' => $hasMoq ? fake()->randomElement(['meter', 'kg']) : null,
            'certification' => fake()->optional(0.5)->sentence(),
            'compliance_standard' => fake()->optional(0.5)->sentence(),
            'chemical_info' => fake()->optional(0.5)->paragraph(),
            'production_stages' => fake()->boolean(50) ? [
                ['title' => fake()->words(3, true), 'description' => fake()->sentence()],
            ] : null,
            'sort_order' => 0,
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
