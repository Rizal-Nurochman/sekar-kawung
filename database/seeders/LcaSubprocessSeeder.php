<?php

namespace Database\Seeders;

use App\Models\Subprocess;
use Illuminate\Database\Seeder;

class LcaSubprocessSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, process_id: int, description: string, sort_order: int}>
     */
    private const array SUBPROCESSES = [
        ['id' => 1, 'process_id' => 1, 'sort_order' => 1, 'description' => 'Mengolah Tanah/media tanam'],
        ['id' => 2, 'process_id' => 1, 'sort_order' => 2, 'description' => 'Proses Pemeliharaan Tanaman Kapas'],
        ['id' => 3, 'process_id' => 1, 'sort_order' => 3, 'description' => 'Proses Pemanenan'],
        ['id' => 4, 'process_id' => 1, 'sort_order' => 4, 'description' => 'Proses Paska Panen'],
        ['id' => 5, 'process_id' => 1, 'sort_order' => 5, 'description' => 'Proses Pembuatan Benang – hingga Jadi kain'],
        ['id' => 6, 'process_id' => 2, 'sort_order' => 1, 'description' => 'Proses Membatik tulis tangan'],
        ['id' => 7, 'process_id' => 2, 'sort_order' => 2, 'description' => 'Proses Pewarnaan Batik (Biru)'],
        ['id' => 8, 'process_id' => 2, 'sort_order' => 3, 'description' => 'Proses Pewarnaan Batik (Cokelat)'],
        ['id' => 9, 'process_id' => 2, 'sort_order' => 4, 'description' => 'Proses Pewarnaan Batik (Warna Lainnya)'],
        ['id' => 10, 'process_id' => 3, 'sort_order' => 1, 'description' => 'Mengolah Tanah/media tanam'],
        ['id' => 11, 'process_id' => 3, 'sort_order' => 2, 'description' => 'Proses Pemeliharaan Tanaman Kapas'],
        ['id' => 12, 'process_id' => 3, 'sort_order' => 3, 'description' => 'Proses Pemanenan'],
        ['id' => 13, 'process_id' => 3, 'sort_order' => 4, 'description' => 'Proses Paska Panen'],
        ['id' => 14, 'process_id' => 3, 'sort_order' => 5, 'description' => 'Proses Pembuatan Benang – hingga Jadi kain'],
        ['id' => 15, 'process_id' => 4, 'sort_order' => 1, 'description' => 'Proses Membatik tulis tangan'],
        ['id' => 16, 'process_id' => 4, 'sort_order' => 2, 'description' => 'Proses Pewarnaan Batik (Biru)'],
        ['id' => 17, 'process_id' => 4, 'sort_order' => 3, 'description' => 'Proses Pewarnaan Batik (Putih)'],
    ];

    public function run(): void
    {
        Subprocess::upsert(
            self::SUBPROCESSES,
            uniqueBy: ['id'],
            update: ['process_id', 'description', 'sort_order'],
        );
    }
}
