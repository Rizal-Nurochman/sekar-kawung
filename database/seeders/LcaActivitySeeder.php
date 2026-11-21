<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class LcaActivitySeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, subprocess_id: int, sort_order: int, activity_unit_id: int|null, emission_factor_id: int|null, description: string}>
     */
    private const array ACTIVITIES = [
        ['id' => 1, 'subprocess_id' => 1, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Cangkul'],
        ['id' => 2, 'subprocess_id' => 1, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 3, 'subprocess_id' => 2, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Pengairan tadah hujan/rain feed'],
        ['id' => 4, 'subprocess_id' => 2, 'sort_order' => 2, 'activity_unit_id' => 2, 'emission_factor_id' => 2, 'description' => 'Pengairan artificial (pompa diesel)'],
        ['id' => 5, 'subprocess_id' => 2, 'sort_order' => 3, 'activity_unit_id' => 3, 'emission_factor_id' => 3, 'description' => 'Pengairan artificial (pompa listrik)'],
        ['id' => 6, 'subprocess_id' => 2, 'sort_order' => 4, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 7, 'subprocess_id' => 2, 'sort_order' => 5, 'activity_unit_id' => 4, 'emission_factor_id' => 4, 'description' => 'Pestisida organic mandiri'],
        ['id' => 8, 'subprocess_id' => 3, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Handpicked'],
        ['id' => 9, 'subprocess_id' => 3, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Sun dried'],
        ['id' => 10, 'subprocess_id' => 3, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Stored naturally/no electric humidity control'],
        ['id' => 11, 'subprocess_id' => 4, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand-ginning'],
        ['id' => 12, 'subprocess_id' => 4, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand carding'],
        ['id' => 13, 'subprocess_id' => 5, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand spinning'],
        ['id' => 14, 'subprocess_id' => 5, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Hand-weaving'],
        ['id' => 15, 'subprocess_id' => 6, 'sort_order' => 1, 'activity_unit_id' => 5, 'emission_factor_id' => 5, 'description' => 'Input malam (dibeli)'],
        ['id' => 16, 'subprocess_id' => 6, 'sort_order' => 2, 'activity_unit_id' => 6, 'emission_factor_id' => 6, 'description' => 'Input Lampu Penerangan'],
        ['id' => 17, 'subprocess_id' => 6, 'sort_order' => 3, 'activity_unit_id' => 7, 'emission_factor_id' => 7, 'description' => 'Input Gas u/ Kompor'],
        ['id' => 18, 'subprocess_id' => 7, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Temanggung'],
        ['id' => 19, 'subprocess_id' => 7, 'sort_order' => 2, 'activity_unit_id' => 7, 'emission_factor_id' => 8, 'description' => 'Gas untuk perebusan'],
        ['id' => 20, 'subprocess_id' => 7, 'sort_order' => 3, 'activity_unit_id' => 7, 'emission_factor_id' => 7, 'description' => 'Pasta Strobilanthes cussia'],
        ['id' => 21, 'subprocess_id' => 7, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur Matahari'],
        ['id' => 22, 'subprocess_id' => 8, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Limbah gergajian mahoni'],
        ['id' => 23, 'subprocess_id' => 8, 'sort_order' => 2, 'activity_unit_id' => 9, 'emission_factor_id' => 13, 'description' => 'Kayu Bakar'],
        ['id' => 24, 'subprocess_id' => 8, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur matahari'],
        ['id' => 25, 'subprocess_id' => 9, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke DIY'],
        ['id' => 26, 'subprocess_id' => 9, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Daun/kulit kayu/akar/bunga'],
        ['id' => 27, 'subprocess_id' => 9, 'sort_order' => 3, 'activity_unit_id' => 9, 'emission_factor_id' => 13, 'description' => 'Kayu Bakar'],
        ['id' => 28, 'subprocess_id' => 9, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur matahari'],
        ['id' => 29, 'subprocess_id' => 10, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Cangkul'],
        ['id' => 30, 'subprocess_id' => 10, 'sort_order' => 2, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 31, 'subprocess_id' => 11, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Pengairan tadah hujan/rain feed'],
        ['id' => 32, 'subprocess_id' => 11, 'sort_order' => 2, 'activity_unit_id' => 2, 'emission_factor_id' => 2, 'description' => 'Pengairan artificial (pompa diesel)'],
        ['id' => 33, 'subprocess_id' => 11, 'sort_order' => 3, 'activity_unit_id' => 3, 'emission_factor_id' => 3, 'description' => 'Pengairan artificial (pompa listrik)'],
        ['id' => 34, 'subprocess_id' => 11, 'sort_order' => 4, 'activity_unit_id' => 1, 'emission_factor_id' => 1, 'description' => 'Pupuk organic mandiri'],
        ['id' => 35, 'subprocess_id' => 11, 'sort_order' => 5, 'activity_unit_id' => 4, 'emission_factor_id' => 4, 'description' => 'Pestisida organic mandiri'],
        ['id' => 36, 'subprocess_id' => 12, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Handpicked'],
        ['id' => 37, 'subprocess_id' => 12, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Sun dried'],
        ['id' => 38, 'subprocess_id' => 12, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Stored naturally/no electric humidity control'],
        ['id' => 39, 'subprocess_id' => 13, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Sekaralas/Ngawi'],
        ['id' => 40, 'subprocess_id' => 13, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Bensin untuk mesin ginning'],
        ['id' => 41, 'subprocess_id' => 13, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Klaten'],
        ['id' => 42, 'subprocess_id' => 13, 'sort_order' => 4, 'activity_unit_id' => 6, 'emission_factor_id' => 6, 'description' => 'Listrik untuk mesin carding'],
        ['id' => 43, 'subprocess_id' => 14, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Pengiriman sliver ke Transport ke pabrik di Pekalongan/Bandung/Solo'],
        ['id' => 44, 'subprocess_id' => 14, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Spinning dg mesin pabrik'],
        ['id' => 45, 'subprocess_id' => 14, 'sort_order' => 3, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Klaten'],
        ['id' => 46, 'subprocess_id' => 14, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Tenun Tangan ATBM di Desa Karangasem Klaten'],
        ['id' => 47, 'subprocess_id' => 14, 'sort_order' => 5, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport kembali ke Yogya'],
        ['id' => 48, 'subprocess_id' => 15, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Tancep/DIY'],
        ['id' => 49, 'subprocess_id' => 15, 'sort_order' => 2, 'activity_unit_id' => 5, 'emission_factor_id' => 5, 'description' => 'Input malam di beli'],
        ['id' => 50, 'subprocess_id' => 15, 'sort_order' => 3, 'activity_unit_id' => 6, 'emission_factor_id' => 3, 'description' => 'Input Lampu Penerangan'],
        ['id' => 51, 'subprocess_id' => 15, 'sort_order' => 4, 'activity_unit_id' => 7, 'emission_factor_id' => 7, 'description' => 'Input Gas u/ Kompor'],
        ['id' => 52, 'subprocess_id' => 16, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke Temanggung'],
        ['id' => 53, 'subprocess_id' => 16, 'sort_order' => 2, 'activity_unit_id' => 7, 'emission_factor_id' => 7, 'description' => 'Gas untuk perebusan'],
        ['id' => 54, 'subprocess_id' => 16, 'sort_order' => 3, 'activity_unit_id' => 7, 'emission_factor_id' => 7, 'description' => 'Pasta Strobilanthes cussia'],
        ['id' => 55, 'subprocess_id' => 16, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur Matahari'],
        ['id' => 56, 'subprocess_id' => 17, 'sort_order' => 1, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Transport ke DIY'],
        ['id' => 57, 'subprocess_id' => 17, 'sort_order' => 2, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Daun/kulit kayu/akar/bunga'],
        ['id' => 58, 'subprocess_id' => 17, 'sort_order' => 3, 'activity_unit_id' => 9, 'emission_factor_id' => 16, 'description' => 'Kayu Bakar'],
        ['id' => 59, 'subprocess_id' => 17, 'sort_order' => 4, 'activity_unit_id' => null, 'emission_factor_id' => null, 'description' => 'Jemur matahari'],
    ];

    public function run(): void
    {
        Activity::upsert(
            self::ACTIVITIES,
            uniqueBy: ['id'],
            update: ['subprocess_id', 'activity_unit_id', 'emission_factor_id', 'sort_order', 'description'],
        );
    }
}
