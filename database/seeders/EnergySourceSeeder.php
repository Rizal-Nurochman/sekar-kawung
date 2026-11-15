<?php

namespace Database\Seeders;

use App\Models\EnergySource;
use Illuminate\Database\Seeder;

class EnergySourceSeeder extends Seeder
{
    /**
     * Reference data for the calculator; the solar factor is 0.25x, not zero.
     */
    public function run(): void
    {
        $energySources = [
            [
                'name' => 'Grid Listrik Standar',
                'description' => 'PLN / Grid Industri',
                'slug' => 'grid',
                'emission_multiplier' => '1.000',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Energi Terbarukan / Panel Surya',
                'description' => 'Panel surya dan energi terbarukan',
                'slug' => 'solar',
                'emission_multiplier' => '0.250',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Campuran Batubara & Listrik',
                'description' => 'Campuran batubara dan listrik grid',
                'slug' => 'coal_mix',
                'emission_multiplier' => '1.400',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($energySources as $energySource) {
            EnergySource::query()->updateOrCreate(
                ['slug' => $energySource['slug']],
                $energySource,
            );
        }
    }
}
