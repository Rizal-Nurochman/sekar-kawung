<?php

namespace Database\Seeders;

use App\Models\Boundary;
use Illuminate\Database\Seeder;

class LcaBoundarySeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, scheme_id: int, description: string, sort_order: int}>
     */
    private const array BOUNDARIES = [
        [
            'id' => 1,
            'scheme_id' => 1,
            'sort_order' => 1,
            'description' => 'Proses Produksi dari Biji Sampai Jadi Kain Gedog Tuban',
        ],
        [
            'id' => 2,
            'scheme_id' => 1,
            'sort_order' => 2,
            'description' => 'Proses dari Kain Gedog Tuban sampai Kain Batik Gedog Tuban',
        ],
        [
            'id' => 3,
            'scheme_id' => 2,
            'sort_order' => 1,
            'description' => 'Proses Produksi dari Biji Sampai Jadi Kain Tenun ATBM Pisano',
        ],
        [
            'id' => 4,
            'scheme_id' => 2,
            'sort_order' => 2,
            'description' => 'Proses dari Kain Tenun ATBM Pisano sampai Kain Batik Tenun ATBM Pisano',
        ],
    ];

    public function run(): void
    {
        Boundary::upsert(
            self::BOUNDARIES,
            uniqueBy: ['id'],
            update: ['scheme_id', 'description', 'sort_order'],
        );
    }
}
