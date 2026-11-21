<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class LcaOptionSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, activity_unit_id: int|null, emission_factor_id: int|null, description: string}>
     */
    private const array OPTIONS = [
        ['id' => 1, 'activity_unit_id' => 8, 'emission_factor_id' => 9, 'description' => 'Sepeda Motor'],
        ['id' => 2, 'activity_unit_id' => 8, 'emission_factor_id' => 10, 'description' => 'Mobil-Bensin'],
        ['id' => 3, 'activity_unit_id' => 8, 'emission_factor_id' => 11, 'description' => 'Mobil-Diesel'],
        ['id' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Tosa'],
        ['id' => 5, 'activity_unit_id' => 8, 'emission_factor_id' => 12, 'description' => 'Truk'],
        ['id' => 6, 'activity_unit_id' => 10, 'emission_factor_id' => 14, 'description' => 'RON 92'],
        ['id' => 7, 'activity_unit_id' => 10, 'emission_factor_id' => 15, 'description' => 'RON 98'],
    ];

    public function run(): void
    {
        Option::upsert(
            self::OPTIONS,
            uniqueBy: ['id'],
            update: ['activity_unit_id', 'emission_factor_id', 'description'],
        );
    }
}
