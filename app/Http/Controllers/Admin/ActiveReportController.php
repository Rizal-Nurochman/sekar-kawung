<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ToggleActive;
use App\Http\Controllers\Controller;
use App\Models\SustainabilityReport;
use Illuminate\Http\RedirectResponse;

class ActiveReportController extends Controller
{
    public function store(SustainabilityReport $report, ToggleActive $toggle): RedirectResponse
    {
        $toggle->handle($report, true);

        return back()->with('status', 'Laporan berhasil diaktifkan.');
    }

    public function destroy(SustainabilityReport $report, ToggleActive $toggle): RedirectResponse
    {
        $toggle->handle($report, false);

        return back()->with('status', 'Laporan dinonaktifkan.');
    }
}
