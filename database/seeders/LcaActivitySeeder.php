<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class LcaActivitySeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, process_id: int, sort_order: int, activity_unit_id: int|null, emission_factor_id: int|null, description: string}>
     */
    private const array ACTIVITIES = [
        ['id' => 1, 'process_id' => 1, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Cangkul'],
        ['id' => 2, 'process_id' => 1, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 3, 'process_id' => 2, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Pengairan tadah hujan/rain feed'],
        ['id' => 4, 'process_id' => 2, 'sort_order' => 2, 'activity_unit_id' => 2, 'emission_factor_id' => 2, 'description' => 'Pengairan artificial (pompa diesel)'],
        ['id' => 5, 'process_id' => 2, 'sort_order' => 3, 'activity_unit_id' => 6, 'emission_factor_id' => 3, 'description' => 'Pengairan artificial (pompa listrik)'],
        ['id' => 6, 'process_id' => 2, 'sort_order' => 4, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 7, 'process_id' => 2, 'sort_order' => 5, 'activity_unit_id' => 1, 'emission_factor_id' => 4, 'description' => 'Pestisida organic mandiri'],
        ['id' => 8, 'process_id' => 3, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Handpicked'],
        ['id' => 9, 'process_id' => 3, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Sun dried'],
        ['id' => 10, 'process_id' => 3, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Stored naturally/no electric humidity control'],
        ['id' => 11, 'process_id' => 4, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand-ginning'],
        ['id' => 12, 'process_id' => 4, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand carding'],
        ['id' => 13, 'process_id' => 5, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand spinning'],
        ['id' => 14, 'process_id' => 5, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand-weaving'],
        ['id' => 15, 'process_id' => 6, 'sort_order' => 1, 'activity_unit_id' => 1, 'emission_factor_id' => 5, 'description' => 'Input malam (dibeli)'],
        ['id' => 16, 'process_id' => 6, 'sort_order' => 2, 'activity_unit_id' => 6, 'emission_factor_id' => 6, 'description' => 'Input Lampu Penerangan'],
        ['id' => 17, 'process_id' => 6, 'sort_order' => 3, 'activity_unit_id' => 1, 'emission_factor_id' => 7, 'description' => 'Input Gas u/ Kompor'],
        ['id' => 18, 'process_id' => 7, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Temanggung'],
        ['id' => 19, 'process_id' => 7, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 8, 'description' => 'Gas untuk perebusan'],
        ['id' => 20, 'process_id' => 7, 'sort_order' => 3, 'activity_unit_id' => 1, 'emission_factor_id' => 7, 'description' => 'Pasta Strobilanthes cussia'],
        ['id' => 21, 'process_id' => 7, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur Matahari'],
        ['id' => 22, 'process_id' => 8, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Limbah gergajian mahoni'],
        ['id' => 23, 'process_id' => 8, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 13, 'description' => 'Kayu Bakar'],
        ['id' => 24, 'process_id' => 8, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur matahari'],
        ['id' => 25, 'process_id' => 9, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke DIY'],
        ['id' => 26, 'process_id' => 9, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Daun/kulit kayu/akar/bunga'],
        ['id' => 27, 'process_id' => 9, 'sort_order' => 3, 'activity_unit_id' => 1, 'emission_factor_id' => 13, 'description' => 'Kayu Bakar'],
        ['id' => 28, 'process_id' => 9, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur matahari'],
        ['id' => 29, 'process_id' => 10, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Cangkul'],
        ['id' => 30, 'process_id' => 10, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 31, 'process_id' => 11, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Pengairan tadah hujan/rain feed'],
        ['id' => 32, 'process_id' => 11, 'sort_order' => 2, 'activity_unit_id' => 2, 'emission_factor_id' => 2, 'description' => 'Pengairan artificial (pompa diesel)'],
        ['id' => 33, 'process_id' => 11, 'sort_order' => 3, 'activity_unit_id' => 6, 'emission_factor_id' => 3, 'description' => 'Pengairan artificial (pompa listrik)'],
        ['id' => 34, 'process_id' => 11, 'sort_order' => 4, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 35, 'process_id' => 11, 'sort_order' => 5, 'activity_unit_id' => 1, 'emission_factor_id' => 4, 'description' => 'Pestisida organic mandiri'],
        ['id' => 36, 'process_id' => 12, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Handpicked'],
        ['id' => 37, 'process_id' => 12, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Sun dried'],
        ['id' => 38, 'process_id' => 12, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Stored naturally/no electric humidity control'],
        ['id' => 39, 'process_id' => 13, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Sekaralas/Ngawi'],
        ['id' => 40, 'process_id' => 13, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Bensin untuk mesin ginning'],
        ['id' => 41, 'process_id' => 13, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Klaten'],
        ['id' => 42, 'process_id' => 13, 'sort_order' => 4, 'activity_unit_id' => 6, 'emission_factor_id' => 6, 'description' => 'Listrik untuk mesin carding'],
        ['id' => 43, 'process_id' => 14, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Pengiriman sliver ke Transport ke pabrik di Pekalongan/Bandung/Solo'],
        ['id' => 44, 'process_id' => 14, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Spinning dg mesin pabrik'],
        ['id' => 45, 'process_id' => 14, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Klaten'],
        ['id' => 46, 'process_id' => 14, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Tenun Tangan ATBM di Desa Karangasem Klaten'],
        ['id' => 47, 'process_id' => 14, 'sort_order' => 5, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport kembali ke Yogya'],
        ['id' => 48, 'process_id' => 15, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Tancep/DIY'],
        ['id' => 49, 'process_id' => 15, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 5, 'description' => 'Input malam di beli'],
        ['id' => 50, 'process_id' => 15, 'sort_order' => 3, 'activity_unit_id' => 6, 'emission_factor_id' => 3, 'description' => 'Input Lampu Penerangan'],
        ['id' => 51, 'process_id' => 15, 'sort_order' => 4, 'activity_unit_id' => 1, 'emission_factor_id' => 7, 'description' => 'Input Gas u/ Kompor'],
        ['id' => 52, 'process_id' => 16, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Temanggung'],
        ['id' => 53, 'process_id' => 16, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 7, 'description' => 'Gas untuk perebusan'],
        ['id' => 54, 'process_id' => 16, 'sort_order' => 3, 'activity_unit_id' => 1, 'emission_factor_id' => 7, 'description' => 'Pasta Strobilanthes cussia'],
        ['id' => 55, 'process_id' => 16, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur Matahari'],
        ['id' => 56, 'process_id' => 17, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke DIY'],
        ['id' => 57, 'process_id' => 17, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Daun/kulit kayu/akar/bunga'],
        ['id' => 58, 'process_id' => 17, 'sort_order' => 3, 'activity_unit_id' => 1, 'emission_factor_id' => 16, 'description' => 'Kayu Bakar'],
        ['id' => 59, 'process_id' => 17, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur matahari'],
    ];

    public function run(): void
    {
        Activity::upsert(
            self::ACTIVITIES,
            uniqueBy: ['id'],
            update: ['process_id', 'activity_unit_id', 'emission_factor_id', 'sort_order', 'description'],
        );
    }
}
