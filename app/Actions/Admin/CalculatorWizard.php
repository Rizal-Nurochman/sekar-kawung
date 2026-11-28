<?php

namespace App\Actions\Admin;

use App\Models\Activity;
use App\Models\Boundary;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CalculatorWizard
{
    public const SESSION_KEY = 'admin_calculator_wizard';

    public function __construct(protected CalculateEmissions $calculator) {}

    /**
     * All boundaries in wizard order: scheme sort, then boundary sort.
     *
     * @return Collection<int, Boundary>
     */
    public function orderedBoundaries(): Collection
    {
        return Boundary::query()
            ->with('scheme')
            ->join('schemes', 'schemes.id', '=', 'boundaries.scheme_id')
            ->orderBy('schemes.sort_order')
            ->orderBy('schemes.id')
            ->orderBy('boundaries.sort_order')
            ->orderBy('boundaries.id')
            ->select('boundaries.*')
            ->get();
    }

    /**
     * Load one boundary with its full inventory, ordered.
     */
    public function boundaryWithInventory(Boundary $boundary): Boundary
    {
        return Boundary::query()
            ->with([
                'scheme',
                'processes' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'processes.activities' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'processes.activities.activityUnit',
                'processes.activities.emissionFactor.source',
                'processes.activities.options' => fn ($query) => $query->orderBy('options.id'),
                'processes.activities.options.activityUnit',
                'processes.activities.options.emissionFactor.source',
            ])
            ->findOrFail($boundary->getKey());
    }

    /**
     * @return array{current: int, entries: array<string, array{option_id: ?int, quantity: float}>}|null
     */
    public function state(): ?array
    {
        $state = session()->get(self::SESSION_KEY);

        if (! is_array($state) || ! isset($state['current'], $state['entries'])) {
            return null;
        }

        return [
            'current' => (int) $state['current'],
            'entries' => is_array($state['entries']) ? $state['entries'] : [],
        ];
    }

    public function start(): void
    {
        session()->put(self::SESSION_KEY, [
            'current' => 0,
            'entries' => [],
        ]);
    }

    public function reset(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @param  array<int, array{activity_id: int|string, option_id?: int|string|null, quantity?: float|int|string|null}>  $items
     */
    public function saveStep(Boundary $boundary, array $items): void
    {
        $state = $this->state() ?? ['current' => 0, 'entries' => []];
        $entries = $state['entries'];

        foreach ($items as $row) {
            if (! is_array($row) || ! isset($row['activity_id']) || $row['activity_id'] === '') {
                continue;
            }

            $activityId = (string) (int) $row['activity_id'];
            $optionId = isset($row['option_id']) && $row['option_id'] !== null && $row['option_id'] !== ''
                ? (int) $row['option_id']
                : null;
            $quantity = isset($row['quantity']) && $row['quantity'] !== null && $row['quantity'] !== ''
                ? (float) $row['quantity']
                : 0.0;

            $entries[$activityId] = [
                'option_id' => $optionId,
                'quantity' => $quantity,
            ];
        }

        $state['entries'] = $entries;
        session()->put(self::SESSION_KEY, $state);
    }

    public function moveTo(int $index, int $totalSteps): void
    {
        $state = $this->state() ?? ['current' => 0, 'entries' => []];
        $state['current'] = max(0, min($index, $totalSteps));
        session()->put(self::SESSION_KEY, $state);
    }

    /**
     * Per-scheme totals for saved entries outside the given boundary.
     *
     * @param  array<string, array{option_id: ?int, quantity: float}>  $entries
     * @return array<int, array{id: int, name: string, total: float}>
     */
    public function baseTotals(Boundary $currentBoundary, array $entries): array
    {
        $currentActivityIds = $currentBoundary->processes
            ->flatMap(fn ($process) => $process->activities->pluck('id'))
            ->map(fn ($id): string => (string) (int) $id)
            ->all();

        $currentSet = array_flip($currentActivityIds);

        $otherItems = [];

        foreach ($entries as $activityId => $entry) {
            if (isset($currentSet[(string) $activityId])) {
                continue;
            }

            $otherItems[] = [
                'activity_id' => (int) $activityId,
                'option_id' => $entry['option_id'] ?? null,
                'quantity' => (float) ($entry['quantity'] ?? 0),
            ];
        }

        $calculation = $this->calculator->handle(['items' => $otherItems]);

        $schemeByActivity = Activity::query()
            ->with('process.boundary.scheme')
            ->whereIn('id', collect($otherItems)->pluck('activity_id')->all())
            ->get()
            ->mapWithKeys(function (Activity $activity): array {
                $scheme = $activity->process?->boundary?->scheme;

                return [(int) $activity->getKey() => $scheme === null ? null : [
                    'id' => (int) $scheme->getKey(),
                    'name' => (string) $scheme->description,
                ]];
            });

        $schemes = [];

        foreach ($calculation['items'] ?? [] as $item) {
            $scheme = $schemeByActivity->get((int) $item['activity_id']);

            if ($scheme === null) {
                continue;
            }

            $schemes[$scheme['id']] ??= [
                'id' => $scheme['id'],
                'name' => $scheme['name'],
                'total' => 0.0,
            ];

            $schemes[$scheme['id']]['total'] += (float) $item['emission_result'];
        }

        return array_values($schemes);
    }

    /**
     * Enriched review data across all boundaries.
     *
     * @param  array<string, array{option_id: ?int, quantity: float}>  $entries
     * @return array{
     *     summaries: Collection,
     *     boundaries: Collection,
     *     stages: Collection,
     *     items: Collection,
     *     scheme_totals: Collection,
     *     hotspot_by_scheme: array,
     *     filled: int,
     *     totalActivities: int,
     *     sources: Collection,
     * }
     */
    public function reviewData(): array
    {
        $ordered = $this->orderedBoundaries();
        $state = $this->state();
        $entries = $state['entries'] ?? [];

        $boundaryIds = $ordered->pluck('id')->all();

        $inventory = Boundary::query()
            ->with([
                'scheme',
                'processes' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'processes.activities' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'processes.activities.activityUnit',
                'processes.activities.emissionFactor.source',
                'processes.activities.options' => fn ($query) => $query->orderBy('options.id'),
                'processes.activities.options.activityUnit',
                'processes.activities.options.emissionFactor.source',
            ])
            ->whereIn('id', $boundaryIds)
            ->get()
            ->sortBy([
                fn ($a, $b): int => [$a->scheme->sort_order, $a->scheme->id, $a->sort_order, $a->id] <=> [$b->scheme->sort_order, $b->scheme->id, $b->sort_order, $b->id],
            ])
            ->values();

        $allItems = [];
        foreach ($inventory as $boundary) {
            foreach ($boundary->processes as $process) {
                foreach ($process->activities as $activity) {
                    $key = (string) $activity->getKey();
                    $saved = $entries[$key] ?? ['option_id' => null, 'quantity' => 0.0];
                    $optionId = $saved['option_id'] ?? null;
                    $quantity = (float) ($saved['quantity'] ?? 0);

                    $option = $optionId !== null
                        ? $activity->options->firstWhere('id', (int) $optionId)
                        : null;

                    if ($option !== null) {
                        $factor = (float) ($option->emissionFactor?->ef_value ?? 0);
                        $unit = $option->activityUnit?->unit_name ?? $activity->activityUnit?->unit_name ?? '';
                        $source = $option->emissionFactor?->source;
                    } else {
                        $factor = (float) ($activity->emissionFactor?->ef_value ?? 0);
                        $unit = $activity->activityUnit?->unit_name ?? '';
                        $source = $activity->emissionFactor?->source;
                        $optionId = null;
                    }

                    $allItems[] = [
                        'activity_id' => $activity->getKey(),
                        'option_id' => $optionId,
                        'quantity' => $quantity,
                        'emission_result' => $quantity * $factor,
                    ];
                }
            }
        }

        $calculation = $this->calculator->handle(['items' => $allItems]);
        $calculatedById = collect($calculation['items'] ?? [])->keyBy('activity_id');

        $enriched = collect();
        $stages = collect();

        foreach ($inventory as $boundary) {
            $boundaryTotal = 0.0;
            $boundaryFilled = 0;
            $boundaryCount = 0;

            foreach ($boundary->processes as $process) {
                $stageTotal = 0.0;
                $stageCount = 0;

                foreach ($process->activities as $activity) {
                    $calc = $calculatedById->get($activity->getKey());
                    $quantity = (float) ($calc['quantity'] ?? 0);
                    $emission = (float) ($calc['emission_result'] ?? 0);
                    $optionId = $calc['option_id'] ?? null;

                    $option = $optionId !== null
                        ? $activity->options->firstWhere('id', (int) $optionId)
                        : null;

                    if ($option !== null) {
                        $factor = (float) ($option->emissionFactor?->ef_value ?? 0);
                        $unit = $option->activityUnit?->unit_name ?? $activity->activityUnit?->unit_name ?? '';
                        $source = $option->emissionFactor?->source;
                        $optionName = $option->description;
                    } else {
                        $factor = (float) ($activity->emissionFactor?->ef_value ?? 0);
                        $unit = $activity->activityUnit?->unit_name ?? '';
                        $source = $activity->emissionFactor?->source;
                        $optionName = null;
                    }

                    $isHeritage = $activity->emissionFactor === null && $activity->options->isEmpty();

                    $enriched->push([
                        'activity_id' => $activity->getKey(),
                        'activity' => $activity->description,
                        'process' => $process->description,
                        'boundary' => $boundary->description,
                        'scheme' => $boundary->scheme?->description,
                        'option' => $optionName,
                        'quantity' => $quantity,
                        'unit' => $unit,
                        'factor' => $factor,
                        'source' => $source?->title,
                        'source_link' => $source?->link_reference,
                        'emission' => $emission,
                        'is_heritage' => $isHeritage,
                    ]);

                    $stageTotal += $emission;
                    $stageCount++;
                    $boundaryTotal += $emission;
                    $boundaryCount++;

                    if ($quantity > 0) {
                        $boundaryFilled++;
                    }
                }

                $stages->push([
                    'boundary' => $boundary->description,
                    'scheme' => $boundary->scheme?->description,
                    'name' => $process->description,
                    'total' => $stageTotal,
                    'count' => $stageCount,
                ]);
            }
        }

        $total = (float) ($calculation['total'] ?? 0);
        $filled = $enriched->where(fn ($row): bool => (float) $row['quantity'] > 0)->count();

        $boundarySummaries = $inventory->map(function (Boundary $boundary) use ($enriched): array {
            $rows = $enriched->where(fn ($row): bool => $row['boundary'] === $boundary->description);
            $boundaryTotal = (float) $rows->sum('emission');

            return [
                'id' => $boundary->getKey(),
                'scheme' => $boundary->scheme?->description,
                'description' => $boundary->description,
                'total' => $boundaryTotal,
                'count' => $rows->count(),
                'filled' => $rows->where(fn ($row): bool => (float) $row['quantity'] > 0)->count(),
            ];
        })->values();

        // Per-boundary cumulative scheme subtotals: within one scheme,
        // boundary 2 includes boundary 1 (PRD progressive Cradle-to-Gate).
        $cumulativeByScheme = [];
        $boundarySections = $inventory->map(function (Boundary $boundary) use ($enriched, $stages, $total, &$cumulativeByScheme): array {
            $rows = $enriched->where(fn ($row): bool => $row['boundary'] === $boundary->description);
            $boundaryTotal = (float) $rows->sum('emission');
            $schemeKey = $boundary->scheme?->description ?? 'Tanpa skema';
            $cumulativeByScheme[$schemeKey] = ($cumulativeByScheme[$schemeKey] ?? 0.0) + $boundaryTotal;

            return [
                'id' => $boundary->getKey(),
                'scheme' => $boundary->scheme?->description,
                'description' => $boundary->description,
                'total' => $boundaryTotal,
                'cumulative' => (float) $cumulativeByScheme[$schemeKey],
                'share' => $total > 0 ? $boundaryTotal / $total * 100 : 0.0,
                'count' => $rows->count(),
                'filled' => $rows->where(fn ($row): bool => (float) $row['quantity'] > 0)->count(),
                'stages' => $stages->where(fn ($stage): bool => $stage['boundary'] === $boundary->description)->values(),
                'items' => $rows->values(),
            ];
        })->values();

        // Per-scheme totals
        $schemeTotals = $enriched->groupBy('scheme')->map(function ($rows, $schemeName) {
            return [
                'name' => $schemeName,
                'total' => (float) $rows->sum('emission'),
                'filled' => $rows->where(fn ($row): bool => (float) $row['quantity'] > 0)->count(),
                'count' => $rows->count(),
            ];
        })->values();

        // Per-scheme hotspots
        $hotspotByScheme = $enriched->groupBy('scheme')->map(function ($rows): ?array {
            return $rows->sortByDesc('emission')->first();
        })->toArray();

        $sources = $inventory
            ->flatMap(fn (Boundary $boundary) => $boundary->processes)
            ->flatMap(fn ($process) => $process->activities)
            ->flatMap(function ($activity) use ($entries): array {
                $key = (string) $activity->getKey();
                $optionId = $entries[$key]['option_id'] ?? null;
                $option = $optionId !== null
                    ? $activity->options->firstWhere('id', (int) $optionId)
                    : null;

                if ($option !== null) {
                    return [$option->emissionFactor?->source];
                }

                return [$activity->emissionFactor?->source];
            })
            ->filter()
            ->unique('id')
            ->values();

        return [
            'summaries' => $boundarySummaries,
            'boundaries' => $boundarySections,
            'stages' => $stages,
            'items' => $enriched,
            'scheme_totals' => $schemeTotals,
            'hotspot_by_scheme' => $hotspotByScheme,
            'filled' => $filled,
            'totalActivities' => $enriched->count(),
            'sources' => $sources,
        ];
    }

    /**
     * Build the payload for UpsertReport from the wizard session.
     *
     * @return array{scheme_id: int, boundary_id: int, title: string, slug: null, summary: null, is_active: true, items: array}
     */
    public function buildStorePayload(): array
    {
        $ordered = $this->orderedBoundaries();

        if ($ordered->isEmpty()) {
            abort(422, 'Belum ada boundary untuk disimpan.');
        }

        $state = $this->state() ?? ['current' => 0, 'entries' => []];
        $entries = $state['entries'] ?? [];

        /** @var Boundary $last */
        $last = $ordered->last();

        $activityIds = Activity::query()
            ->whereHas('process', fn ($query) => $query->whereIn('boundary_id', $ordered->pluck('id')))
            ->pluck('id');

        $items = [];
        foreach ($activityIds as $activityId) {
            $key = (string) $activityId;
            $saved = $entries[$key] ?? ['option_id' => null, 'quantity' => 0.0];

            $items[] = [
                'activity_id' => (int) $activityId,
                'option_id' => $saved['option_id'] ?? null,
                'quantity' => (float) ($saved['quantity'] ?? 0),
            ];
        }

        $timestamp = now()->translatedFormat('j F Y, H:i');
        $title = trim(($last->scheme?->description ?? 'Kalkulator').' — '.$last->description.' — '.$timestamp);

        if (mb_strlen($title) > 255) {
            $title = mb_substr($title, 0, 255);
        }

        return [
            'scheme_id' => (int) $last->scheme_id,
            'boundary_id' => (int) $last->getKey(),
            'title' => $title,
            'slug' => null,
            'summary' => null,
            'is_active' => true,
            'items' => $items,
        ];
    }

    public function activityIdsForBoundary(Boundary $boundary): array
    {
        return Activity::query()
            ->whereHas('process', fn ($query) => $query->where('boundary_id', $boundary->getKey()))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function assertBoundaryInScope(int $boundaryId): bool
    {
        return $this->orderedBoundaries()->contains('id', $boundaryId);
    }

    public function optionBelongsToActivity(int $activityId, int $optionId): bool
    {
        return DB::table('activity_option')
            ->where('activity_id', $activityId)
            ->where('option_id', $optionId)
            ->exists();
    }
}
