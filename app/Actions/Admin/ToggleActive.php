<?php

namespace App\Actions\Admin;

use App\Models\SustainabilityReport;
use Illuminate\Support\Facades\DB;

class ToggleActive
{
    public function handle(SustainabilityReport $report, bool $active): SustainabilityReport
    {
        return DB::transaction(function () use ($report, $active): SustainabilityReport {
            if ($active) {
                SustainabilityReport::query()
                    ->where('id', '!=', $report->getKey())
                    ->update(['is_active' => false]);
            }

            $report->update(['is_active' => $active]);

            return $report->refresh();
        });
    }
}
