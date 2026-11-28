<?php

namespace Database\Seeders;

use App\Models\Scheme;
use Illuminate\Database\Seeder;

class LcaSchemeSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, description: string, sort_order: int}>
     */
    private const array SCHEMES = [
        ['id' => 1, 'description' => 'Biji sampai Kain Batik Gedog Tuban', 'sort_order' => 1],
        ['id' => 2, 'description' => 'Biji sampai Kain Batik Tenun ATBM Pisano', 'sort_order' => 2],
    ];

    public function run(): void
    {
        Scheme::upsert(
            self::SCHEMES,
            uniqueBy: ['id'],
            update: ['description', 'sort_order'],
        );
    }
}
