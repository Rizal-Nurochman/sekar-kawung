<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReportRequest;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\SustainabilityReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = in_array($request->query('status'), ['published', 'draft'], true)
            ? $request->query('status')
            : null;

        $search = trim((string) $request->query('q', ''));

        $reports = SustainabilityReport::query()
            ->with(['scheme', 'process'])
            ->when($status === 'published', fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', fn ($query) => $query->where('is_published', false))
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalReports = SustainabilityReport::query()->count();
        $publishedReports = SustainabilityReport::query()->where('is_published', true)->count();

        return view('admin.reports.index', [
            'reports' => $reports,
            'status' => $status,
            'search' => $search,
            'totalReports' => $totalReports,
            'publishedReports' => $publishedReports,
            'draftReports' => $totalReports - $publishedReports,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SustainabilityReport $report): View
    {
        return view('admin.reports.edit', [
            'report' => $report->load(['scheme', 'process']),
            'schemes' => $this->schemes(),
            'processes' => $this->processes(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * A report is tied to the calculator run that produced it, so its scheme,
     * system boundary, items, and total stay fixed; only the editorial fields
     * can change. The slug always regenerates from the title.
     */
    public function update(UpdateReportRequest $request, SustainabilityReport $report): RedirectResponse
    {
        $validated = $request->safe()->only(['title', 'summary']);
        $validated['is_published'] = $request->boolean('is_published');

        $report->update([
            'title' => $validated['title'],
            'slug' => $this->resolveSlug($validated['title'], $validated['title'], $report->getKey()),
            'summary' => $validated['summary'] ?? null,
            'is_published' => $validated['is_published'],
        ]);

        return redirect()->route('admin.reports.show', $report->refresh())->with('status', 'Laporan berhasil diperbarui.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SustainabilityReport $report): View
    {
        $report->load([
            'scheme',
            'process',
            'items.activity.subprocess.process.scheme',
            'items.activity.activityUnit',
            'items.activity.emissionFactor.source',
            'items.option.activityUnit',
            'items.option.emissionFactor.source',
        ]);

        $orderedProcesses = Process::query()
            ->with('scheme')
            ->join('schemes', 'schemes.id', '=', 'processes.scheme_id')
            ->orderBy('schemes.sort_order')
            ->orderBy('schemes.id')
            ->orderBy('processes.sort_order')
            ->orderBy('processes.id')
            ->select('processes.*')
            ->get();

        $grandTotal = (float) $report->items->sum(fn ($item) => (float) $item->emission_result);

        $boundaries = $orderedProcesses->map(function (Process $process) use ($report): array {
            $rows = $report->items
                ->filter(fn ($item) => (int) ($item->activity?->subprocess?->process_id) === (int) $process->getKey())
                ->values();

            $processTotal = (float) $rows->sum(fn ($item) => (float) $item->emission_result);

            $stages = $rows
                ->groupBy(fn ($item) => $item->activity?->subprocess?->description ?? 'Tahap lain')
                ->map(fn ($items, $name) => [
                    'name' => $name,
                    'total' => (float) $items->sum(fn ($item) => (float) $item->emission_result),
                    'count' => $items->count(),
                ])
                ->sortByDesc('total')
                ->values();

            return [
                'id' => $process->getKey(),
                'scheme_id' => $process->scheme_id,
                'scheme' => $process->scheme?->description,
                'description' => $process->description,
                'total' => $processTotal,
                'count' => $rows->count(),
                'filled' => $rows->where(fn ($item): bool => (float) $item->quantity > 0)->count(),
                'stages' => $stages,
                'items' => $rows,
            ];
        })->values();

        $schemes = $boundaries
            ->groupBy('scheme_id')
            ->map(fn ($processes, $schemeId) => [
                'id' => (int) $schemeId,
                'name' => $processes->first()['scheme'],
                'total' => (float) $processes->sum('total'),
                'count' => (int) $processes->sum('count'),
                'filled' => (int) $processes->sum('filled'),
                'processes' => $processes->values(),
            ])
            ->values();

        $stages = $report->items
            ->groupBy(fn ($item) => $item->activity?->subprocess?->description ?? 'Tahap lain')
            ->map(fn ($items, $name) => [
                'name' => $name,
                'total' => (float) $items->sum(fn ($item) => (float) $item->emission_result),
                'count' => $items->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $stageTotal = (float) $stages->sum('total');

        $hotspot = $report->items->sortByDesc(fn ($item) => (float) $item->emission_result)->first();

        $sources = $report->items
            ->map(fn ($item) => $item->activity?->emissionFactor?->source ?? $item->option?->emissionFactor?->source)
            ->filter()
            ->unique('id')
            ->values();

        return view('admin.reports.show', [
            'report' => $report,
            'schemes' => $schemes,
            'boundaries' => $boundaries,
            'grandTotal' => $grandTotal,
            'stages' => $stages,
            'stageTotal' => $stageTotal,
            'hotspot' => $hotspot,
            'sources' => $sources,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SustainabilityReport $report): RedirectResponse
    {
        $report->items()->delete();
        $report->delete();

        return redirect()->route('admin.reports.index')->with('status', 'Laporan berhasil dihapus.');
    }

    /**
     * @return Collection<int, Scheme>
     */
    protected function schemes()
    {
        return Scheme::query()->orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * @return Collection<int, Process>
     */
    protected function processes()
    {
        return Process::query()->with('scheme')->orderBy('scheme_id')->orderBy('sort_order')->orderBy('id')->get();
    }

    protected function resolveSlug(?string $slug, string $title, mixed $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $title) ?: 'laporan';
        $candidate = $base;
        $counter = 1;

        while (SustainabilityReport::query()->where('slug', $candidate)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $counter++;
            $candidate = $base.'-'.$counter;
        }

        return $candidate;
    }
}
