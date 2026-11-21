<?php

namespace Database\Seeders;

use App\Models\ActivityUnit;
use Illuminate\Database\Seeder;

class LcaActivityUnitSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, unit_name: string}>
     */
    private const array ACTIVITY_UNITS = [
        ['id' => 1, 'unit_name' => 'kg pupuk agregat'],
        ['id' => 2, 'unit_name' => 'Liter solar/diesel'],
        ['id' => 3, 'unit_name' => 'kWh listrik'],
        ['id' => 4, 'unit_name' => 'kg pestisida organik'],
        ['id' => 5, 'unit_name' => 'kg malam (paraffin wax)'],
        ['id' => 6, 'unit_name' => 'kWh'],
        ['id' => 7, 'unit_name' => 'kg LPG'],
        ['id' => 8, 'unit_name' => 'km'],
        ['id' => 9, 'unit_name' => 'kg kayu bakar'],
        ['id' => 10, 'unit_name' => 'Liter bensin'],
    ];

    public function run(): void
    {
        ActivityUnit::upsert(
            self::ACTIVITY_UNITS,
            uniqueBy: ['id'],
            update: ['unit_name'],
        );
    }
}
