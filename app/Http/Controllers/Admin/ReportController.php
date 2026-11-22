<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\UpsertReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReportRequest;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\SustainabilityReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
     * A report is tied to the calculator run that produced it, so its scheme
     * and system boundary stay fixed; only the editorial fields can change.
     */
    public function update(UpdateReportRequest $request, SustainabilityReport $report, UpsertReport $upsert): RedirectResponse
    {
        $validated = $request->safe()->only(['title', 'slug', 'summary', 'is_published']);
        $validated['scheme_id'] = $report->scheme_id;
        $validated['process_id'] = $report->process_id;
        $validated['is_published'] = $request->boolean('is_published');

        $report = $this->persist($upsert, $validated, $report);

        return redirect()->route('admin.reports.show', $report)->with('status', 'Laporan berhasil diperbarui.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SustainabilityReport $report): View
    {
        $report->load(['scheme', 'process', 'items.activity.subprocess', 'items.activity.emissionFactor.source', 'items.option.emissionFactor.source']);

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
     * Persist a report via UpsertReport when available, otherwise fall back
     * to a minimal local creation so this slice stays testable while the
     * UpsertReport action is still being built by another agent.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function persist(UpsertReport $upsert, array $validated, ?SustainabilityReport $report = null): SustainabilityReport
    {
        $data = Arr::only($validated, ['title', 'slug', 'scheme_id', 'process_id', 'summary', 'is_published']);

        if (method_exists($upsert, 'handle')) {
            return $upsert->handle($data, $report);
        }

        return DB::transaction(function () use ($data, $report): SustainabilityReport {
            $attributes = [
                'title' => $data['title'],
                'slug' => $this->resolveSlug($data['slug'] ?? null, $data['title'], $report?->getKey()),
                'scheme_id' => $data['scheme_id'],
                'process_id' => $data['process_id'],
                'summary' => $data['summary'] ?? null,
                'is_published' => (bool) ($data['is_published'] ?? false),
            ];

            if ($report instanceof SustainabilityReport) {
                $report->update($attributes);

                return $report->refresh();
            }

            return SustainabilityReport::query()->create($attributes + ['total_emission' => 0]);
        });
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
