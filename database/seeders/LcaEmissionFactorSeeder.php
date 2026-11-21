<?php

namespace Database\Seeders;

use App\Models\EmissionFactor;
use Illuminate\Database\Seeder;

class LcaEmissionFactorSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, source_id: int, unit_name: string, ef_value: string}>
     */
    private const array EMISSION_FACTORS = [
        ['id' => 1, 'source_id' => 1, 'unit_name' => 'kg N₂O-N', 'ef_value' => '0.0100'],
        ['id' => 2, 'source_id' => 2, 'unit_name' => 'kg CO₂/liter solar/diesel', 'ef_value' => '2.6140'],
        ['id' => 3, 'source_id' => 3, 'unit_name' => 'kg CO₂/kWh', 'ef_value' => '0.8700'],
        ['id' => 4, 'source_id' => 4, 'unit_name' => 'kg CO₂e/kg pestisida organik', 'ef_value' => '0.9000'],
        ['id' => 5, 'source_id' => 5, 'unit_name' => 'kg CO₂/kg malam', 'ef_value' => '0.5900'],
        ['id' => 6, 'source_id' => 6, 'unit_name' => 'kg CO₂/kWh', 'ef_value' => '0.8700'],
        ['id' => 7, 'source_id' => 7, 'unit_name' => 'kg CO₂/kg LPG', 'ef_value' => '3.0850'],
        ['id' => 8, 'source_id' => 7, 'unit_name' => 'CO₂ (g/kg bahan bakar)', 'ef_value' => '3.0850'],
        ['id' => 9, 'source_id' => 8, 'unit_name' => 'CO2 g/km', 'ef_value' => '0.0140'],
        ['id' => 10, 'source_id' => 8, 'unit_name' => 'kg CO/km', 'ef_value' => '0.0400'],
        ['id' => 11, 'source_id' => 8, 'unit_name' => 'kg CO/km', 'ef_value' => '0.0028'],
        ['id' => 12, 'source_id' => 9, 'unit_name' => 'kg CO/km', 'ef_value' => '0.0045'],
        ['id' => 13, 'source_id' => 10, 'unit_name' => 'kg CO2e/kg of materials', 'ef_value' => '1.2610'],
        ['id' => 14, 'source_id' => 11, 'unit_name' => 'kg CO₂/liter bensin', 'ef_value' => '2.3900'],
        ['id' => 15, 'source_id' => 12, 'unit_name' => 'kg CO₂/liter bensin', 'ef_value' => '2.4100'],
        ['id' => 16, 'source_id' => 13, 'unit_name' => 'kg CO2e/kg of materials', 'ef_value' => '1.2610'],
    ];

    public function run(): void
    {
        EmissionFactor::upsert(
            self::EMISSION_FACTORS,
            uniqueBy: ['id'],
            update: ['source_id', 'unit_name', 'ef_value'],
        );
    }
}
