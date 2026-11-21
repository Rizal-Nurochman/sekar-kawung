<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LcaMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call([
                LcaSchemeSeeder::class,
                LcaProcessSeeder::class,
                LcaSubprocessSeeder::class,
                LcaActivityUnitSeeder::class,
                LcaSourceSeeder::class,
                LcaEmissionFactorSeeder::class,
                LcaOptionSeeder::class,
                LcaActivitySeeder::class,
                LcaActivityOptionSeeder::class,
            ]);
        });
    }
}
