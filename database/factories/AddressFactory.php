<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'village' => fake()->city(),
            'regency' => fake()->city(),
            'province' => fake()->state(),
            'country' => 'Indonesia',
            'is_primary' => true,
            'label' => fn (array $attributes) => $attributes['village'].', '.$attributes['regency'],
        ];
    }
}
