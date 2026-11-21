<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LcaActivityOptionSeeder extends Seeder
{
    /**
     * @var array<int, int>
     */
    private const array TRANSPORT_ACTIVITY_IDS = [18, 25, 39, 41, 43, 45, 47, 48, 52, 56];

    /**
     * @var array<int, int>
     */
    private const array TRANSPORT_OPTION_IDS = [1, 2, 3, 4, 5];

    /**
     * @var array<int, int>
     */
    private const array FUEL_ACTIVITY_IDS = [40, 44];

    /**
     * @var array<int, int>
     */
    private const array FUEL_OPTION_IDS = [6, 7];

    public function run(): void
    {
        $activityOptions = [];

        foreach (self::TRANSPORT_ACTIVITY_IDS as $activityId) {
            foreach (self::TRANSPORT_OPTION_IDS as $optionId) {
                $activityOptions[] = [
                    'activity_id' => $activityId,
                    'option_id' => $optionId,
                ];
            }
        }

        foreach (self::FUEL_ACTIVITY_IDS as $activityId) {
            foreach (self::FUEL_OPTION_IDS as $optionId) {
                $activityOptions[] = [
                    'activity_id' => $activityId,
                    'option_id' => $optionId,
                ];
            }
        }

        DB::table('activity_option')->insertOrIgnore($activityOptions);
    }
}
