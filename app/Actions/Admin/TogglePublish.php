<?php

namespace App\Actions\Admin;

use App\Models\SustainabilityReport;

class TogglePublish
{
    public function handle(SustainabilityReport $report, bool $published): SustainabilityReport
    {
        $report->update(['is_published' => $published]);

        return $report->refresh();
    }
}
