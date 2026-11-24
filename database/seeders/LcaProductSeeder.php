<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class LcaProductSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, scheme_id: int, description: string, sort_order: int}>
     */
    private const array PRODUCTS = [
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
            'description' => 'Proses Produksi dari Biji Sampai Jadi Kain Batik Gedog Tuban',
        ],
        [
            'id' => 3,
            'scheme_id' => 2,
            'sort_order' => 1,
            'description' => 'Proses Produksi dari Biji Sampai Jadi Kain TENUN ATBM PISANO',
        ],
        [
            'id' => 4,
            'scheme_id' => 2,
            'sort_order' => 2,
            'description' => 'Proses Produksi dari Biji Sampai Jadi Kain BATIK TENUN ATBM PISANO',
        ],
    ];

    public function run(): void
    {
        Product::upsert(
            self::PRODUCTS,
            uniqueBy: ['id'],
            update: ['scheme_id', 'description', 'sort_order'],
        );
    }
}
