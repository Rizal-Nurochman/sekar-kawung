<?php

namespace Database\Seeders;

use App\Models\Process;
use Illuminate\Database\Seeder;

class LcaProcessSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, product_id: int, description: string, sort_order: int}>
     */
    private const array PROCESSES = [
        ['id' => 1, 'product_id' => 1, 'sort_order' => 1, 'description' => 'Mengolah Tanah/media tanam'],
        ['id' => 2, 'product_id' => 1, 'sort_order' => 2, 'description' => 'Proses Pemeliharaan Tanaman Kapas'],
        ['id' => 3, 'product_id' => 1, 'sort_order' => 3, 'description' => 'Proses Pemanenan'],
        ['id' => 4, 'product_id' => 1, 'sort_order' => 4, 'description' => 'Proses Paska Panen'],
        ['id' => 5, 'product_id' => 1, 'sort_order' => 5, 'description' => 'Proses Pembuatan Benang – hingga Jadi kain'],
        ['id' => 6, 'product_id' => 2, 'sort_order' => 1, 'description' => 'Proses Membatik tulis tangan'],
        ['id' => 7, 'product_id' => 2, 'sort_order' => 2, 'description' => 'Proses Pewarnaan Batik (Biru)'],
        ['id' => 8, 'product_id' => 2, 'sort_order' => 3, 'description' => 'Proses Pewarnaan Batik (Cokelat)'],
        ['id' => 9, 'product_id' => 2, 'sort_order' => 4, 'description' => 'Proses Pewarnaan Batik (Warna Lainnya)'],
        ['id' => 10, 'product_id' => 3, 'sort_order' => 1, 'description' => 'Mengolah Tanah/media tanam'],
        ['id' => 11, 'product_id' => 3, 'sort_order' => 2, 'description' => 'Proses Pemeliharaan Tanaman Kapas'],
        ['id' => 12, 'product_id' => 3, 'sort_order' => 3, 'description' => 'Proses Pemanenan'],
        ['id' => 13, 'product_id' => 3, 'sort_order' => 4, 'description' => 'Proses Paska Panen'],
        ['id' => 14, 'product_id' => 3, 'sort_order' => 5, 'description' => 'Proses Pembuatan Benang – hingga Jadi kain'],
        ['id' => 15, 'product_id' => 4, 'sort_order' => 1, 'description' => 'Proses Membatik tulis tangan'],
        ['id' => 16, 'product_id' => 4, 'sort_order' => 2, 'description' => 'Proses Pewarnaan Batik (Biru)'],
        ['id' => 17, 'product_id' => 4, 'sort_order' => 3, 'description' => 'Proses Pewarnaan Batik (Putih)'],
    ];

    public function run(): void
    {
        Process::upsert(
            self::PROCESSES,
            uniqueBy: ['id'],
            update: ['product_id', 'description', 'sort_order'],
        );
    }
}
