<?php

namespace Database\Seeders;

use App\Models\Process;
use Illuminate\Database\Seeder;

class LcaProcessSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, boundary_id: int, description: string, sort_order: int}>
     */
    private const array PROCESSES = [
        ['id' => 1, 'boundary_id' => 1, 'sort_order' => 1, 'description' => 'Mengolah Tanah/media tanam'],
        ['id' => 2, 'boundary_id' => 1, 'sort_order' => 2, 'description' => 'Proses Pemeliharaan Tanaman Kapas'],
        ['id' => 3, 'boundary_id' => 1, 'sort_order' => 3, 'description' => 'Proses Pemanenan'],
        ['id' => 4, 'boundary_id' => 1, 'sort_order' => 4, 'description' => 'Proses Paska Panen'],
        ['id' => 5, 'boundary_id' => 1, 'sort_order' => 5, 'description' => 'Proses Pembuatan Benang – hingga Jadi kain'],
        ['id' => 6, 'boundary_id' => 2, 'sort_order' => 1, 'description' => 'Proses Membatik tulis tangan'],
        ['id' => 7, 'boundary_id' => 2, 'sort_order' => 2, 'description' => 'Proses Pewarnaan Batik (Biru)'],
        ['id' => 8, 'boundary_id' => 2, 'sort_order' => 3, 'description' => 'Proses Pewarnaan Batik (Cokelat)'],
        ['id' => 9, 'boundary_id' => 2, 'sort_order' => 4, 'description' => 'Proses Pewarnaan Batik (Warna Lainnya)'],
        ['id' => 10, 'boundary_id' => 3, 'sort_order' => 1, 'description' => 'Mengolah Tanah/media tanam'],
        ['id' => 11, 'boundary_id' => 3, 'sort_order' => 2, 'description' => 'Proses Pemeliharaan Tanaman Kapas'],
        ['id' => 12, 'boundary_id' => 3, 'sort_order' => 3, 'description' => 'Proses Pemanenan'],
        ['id' => 13, 'boundary_id' => 3, 'sort_order' => 4, 'description' => 'Proses Paska Panen'],
        ['id' => 14, 'boundary_id' => 3, 'sort_order' => 5, 'description' => 'Proses Pembuatan Benang – hingga Jadi kain'],
        ['id' => 15, 'boundary_id' => 4, 'sort_order' => 1, 'description' => 'Proses Membatik tulis tangan'],
        ['id' => 16, 'boundary_id' => 4, 'sort_order' => 2, 'description' => 'Proses Pewarnaan Batik (Biru)'],
        ['id' => 17, 'boundary_id' => 4, 'sort_order' => 3, 'description' => 'Proses Pewarnaan Batik (Putih)'],
    ];

    public function run(): void
    {
        Process::upsert(
            self::PROCESSES,
            uniqueBy: ['id'],
            update: ['boundary_id', 'description', 'sort_order'],
        );
    }
}
