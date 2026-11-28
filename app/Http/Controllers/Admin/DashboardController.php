<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Option;
use App\Models\Scheme;
use App\Models\SustainabilityReport;
use App\Models\SustainabilityReportItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $totalReports = SustainabilityReport::query()->count();
        $activeReports = SustainabilityReport::query()->where('is_active', true)->count();
        $inactiveReports = $totalReports - $activeReports;
        $totalEmission = (float) SustainabilityReport::query()->where('is_active', true)->sum('total_emission');

        $recentReports = SustainabilityReport::query()
            ->with(['scheme', 'boundary'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $hotspots = SustainabilityReportItem::query()
            ->selectRaw('activity_id, SUM(emission_result) as total')
            ->groupBy('activity_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('activity')
            ->get();

        $schemeCount = Scheme::query()->count();
        $activityCount = Activity::query()->count();
        $optionCount = Option::query()->count();

        return view('admin.dashboard', [
            'totalReports' => $totalReports,
            'activeReports' => $activeReports,
            'inactiveReports' => $inactiveReports,
            'totalEmission' => $totalEmission,
            'recentReports' => $recentReports,
            'hotspots' => $hotspots,
            'schemeCount' => $schemeCount,
            'activityCount' => $activityCount,
            'optionCount' => $optionCount,
        ]);
    }
}
