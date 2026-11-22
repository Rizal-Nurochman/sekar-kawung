<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CalculateEmissions;
use App\Actions\Admin\UpsertReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CalculatorSaveRequest;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\Subprocess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function edit(Request $request): View
    {
        $schemes = Scheme::query()
            ->with(['processes' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $selectedScheme = $schemes->firstWhere('id', (int) $request->query('scheme_id', 0))
            ?? $schemes->first();

        $schemeProcesses = $selectedScheme
            ? $selectedScheme->processes->sortBy([['sort_order', 'asc'], ['id', 'asc']])->values()
            : collect();

        $selectedProcess = $schemeProcesses->firstWhere('id', (int) $request->query('process_id', 0))
            ?? $schemeProcesses->first();

        $subprocesses = collect();

        if ($selectedScheme !== null && $selectedProcess !== null) {
            $processIds = Process::query()
                ->where('scheme_id', $selectedScheme->id)
                ->where('sort_order', '<=', $selectedProcess->sort_order)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id');

            $subprocesses = Subprocess::query()
                ->with([
                    'activities' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                    'activities.activityUnit',
                    'activities.emissionFactor.source',
                    'activities.options' => fn ($query) => $query->orderBy('options.id'),
                    'activities.options.activityUnit',
                    'activities.options.emissionFactor.source',
                ])
                ->whereIn('process_id', $processIds)
                ->orderBy('process_id')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        return view('admin.calculator.edit', [
            'schemes' => $schemes,
            'schemeProcesses' => $schemeProcesses,
            'selectedScheme' => $selectedScheme,
            'selectedProcess' => $selectedProcess,
            'subprocesses' => $subprocesses,
        ]);
    }

    public function preview(CalculatorSaveRequest $request, CalculateEmissions $calculator): JsonResponse|RedirectResponse
    {
        $validated = $request->safe()->only([
            'scheme_id',
            'process_id',
            'title',
            'slug',
            'summary',
            'is_published',
            'items',
        ]);

        $result = $calculator->handle($validated);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json($result);
        }

        return redirect()
            ->route('admin.calculator.edit', [
                'scheme_id' => $validated['scheme_id'],
                'process_id' => $validated['process_id'],
            ])
            ->withInput()
            ->with('previewResult', $result);
    }

    public function store(CalculatorSaveRequest $request, UpsertReport $upsert): RedirectResponse
    {
        $validated = $request->safe()->only([
            'scheme_id',
            'process_id',
            'title',
            'slug',
            'summary',
            'is_published',
            'items',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['items'] = $validated['items'] ?? [];

        $report = $upsert->handle($validated);

        if (Route::has('admin.reports.show')) {
            return redirect()
                ->route('admin.reports.show', $report)
                ->with('status', 'Laporan keberlanjutan berhasil disimpan.');
        }

        return redirect()
            ->route('admin.calculator.edit', [
                'scheme_id' => $report->scheme_id,
                'process_id' => $report->process_id,
            ])
            ->with('status', 'Laporan keberlanjutan berhasil disimpan.');
    }
}
