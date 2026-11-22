<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\TogglePublish;
use App\Http\Controllers\Controller;
use App\Models\SustainabilityReport;
use Illuminate\Http\RedirectResponse;

class PublishedReportController extends Controller
{
    public function store(SustainabilityReport $report, TogglePublish $toggle): RedirectResponse
    {
        $toggle->handle($report, true);

        return back()->with('status', 'Laporan berhasil diterbitkan.');
    }

    public function destroy(SustainabilityReport $report, TogglePublish $toggle): RedirectResponse
    {
        $toggle->handle($report, false);

        return back()->with('status', 'Laporan dikembalikan menjadi draf.');
    }
}
