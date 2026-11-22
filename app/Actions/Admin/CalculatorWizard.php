<?php

namespace App\Actions\Admin;

use App\Models\Activity;
use App\Models\Process;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CalculatorWizard
{
    public const SESSION_KEY = 'admin_calculator_wizard';

    public function __construct(protected CalculateEmissions $calculator) {}

    /**
     * All processes in wizard order: scheme sort, then process sort.
     *
     * @return Collection<int, Process>
     */
    public function orderedProcesses(): Collection
    {
        return Process::query()
            ->with('scheme')
            ->join('schemes', 'schemes.id', '=', 'processes.scheme_id')
            ->orderBy('schemes.sort_order')
            ->orderBy('schemes.id')
            ->orderBy('processes.sort_order')
            ->orderBy('processes.id')
            ->select('processes.*')
            ->get();
    }

    /**
     * Load one process with its full inventory, ordered.
     */
    public function processWithInventory(Process $process): Process
    {
        return Process::query()
            ->with([
                'scheme',
                'subprocesses' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'subprocesses.activities' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'subprocesses.activities.activityUnit',
                'subprocesses.activities.emissionFactor.source',
                'subprocesses.activities.options' => fn ($query) => $query->orderBy('options.id'),
                'subprocesses.activities.options.activityUnit',
                'subprocesses.activities.options.emissionFactor.source',
            ])
            ->findOrFail($process->getKey());
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
    public function saveStep(Process $process, array $items): void
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
     * Totals for saved entries outside the given process.
     *
     * @param  array<string, array{option_id: ?int, quantity: float}>  $entries
     * @return array{total: float, filled: int}
     */
    public function baseTotals(Process $currentProcess, array $entries): array
    {
        $currentActivityIds = $currentProcess->subprocesses
            ->flatMap(fn ($subprocess) => $subprocess->activities->pluck('id'))
            ->map(fn ($id): string => (string) (int) $id)
            ->all();

        $currentSet = array_flip($currentActivityIds);

        $otherItems = [];
        $filled = 0;

        foreach ($entries as $activityId => $entry) {
            if (isset($currentSet[(string) $activityId])) {
                continue;
            }

            $quantity = (float) ($entry['quantity'] ?? 0);

            if ($quantity > 0) {
                $filled++;
            }

            $otherItems[] = [
                'activity_id' => (int) $activityId,
                'option_id' => $entry['option_id'] ?? null,
                'quantity' => $quantity,
            ];
        }

        $calculation = $this->calculator->handle(['items' => $otherItems]);

        return [
            'total' => (float) ($calculation['total'] ?? 0),
            'filled' => $filled,
        ];
    }

    public function totalActivityCount(): int
    {
        return (int) Activity::query()
            ->whereHas('subprocess.process')
            ->count();
    }

    /**
     * Enriched review data across all processes.
     *
     * @param  array<string, array{option_id: ?int, quantity: float}>  $entries
     * @return array{
     *     processes: Collection,
     *     stages: Collection,
     *     items: Collection,
     *     total: float,
     *     filled: int,
     *     totalActivities: int,
     *     hotspot: ?array,
     *     sources: Collection,
     * }
     */
    public function reviewData(): array
    {
        $ordered = $this->orderedProcesses();
        $state = $this->state();
        $entries = $state['entries'] ?? [];

        $processIds = $ordered->pluck('id')->all();

        $inventory = Process::query()
            ->with([
                'scheme',
                'subprocesses' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'subprocesses.activities' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
                'subprocesses.activities.activityUnit',
                'subprocesses.activities.emissionFactor.source',
                'subprocesses.activities.options' => fn ($query) => $query->orderBy('options.id'),
                'subprocesses.activities.options.activityUnit',
                'subprocesses.activities.options.emissionFactor.source',
            ])
            ->whereIn('id', $processIds)
            ->get()
            ->sortBy([
                fn ($a, $b): int => [$a->scheme->sort_order, $a->scheme->id, $a->sort_order, $a->id] <=> [$b->scheme->sort_order, $b->scheme->id, $b->sort_order, $b->id],
            ])
            ->values();

        $allItems = [];
        foreach ($inventory as $process) {
            foreach ($process->subprocesses as $subprocess) {
                foreach ($subprocess->activities as $activity) {
                    $key = (string) $activity->getKey();
                    $saved = $entries[$key] ?? ['option_id' => null, 'quantity' => 0.0];
                    $optionId = $saved['option_id'] ?? null;
                    $quantity = (float) ($saved['quantity'] ?? 0);

                    $option = $optionId !== null
                        ? $activity->options->firstWhere('id', (int) $optionId)
                        : null;

                    if ($option !== null) {
                        $factor = (float) ($option->emissionFactor?->ef_value ?? 0);
                        $unit = $option->activityUnit?->unit_name ?? $activity->activityUnit?->unit_name ?? '–';
                        $source = $option->emissionFactor?->source;
                    } else {
                        $factor = (float) ($activity->emissionFactor?->ef_value ?? 0);
                        $unit = $activity->activityUnit?->unit_name ?? '–';
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

        foreach ($inventory as $process) {
            $processTotal = 0.0;
            $processFilled = 0;
            $processCount = 0;

            foreach ($process->subprocesses as $subprocess) {
                $stageTotal = 0.0;
                $stageCount = 0;

                foreach ($subprocess->activities as $activity) {
                    $calc = $calculatedById->get($activity->getKey());
                    $quantity = (float) ($calc['quantity'] ?? 0);
                    $emission = (float) ($calc['emission_result'] ?? 0);
                    $optionId = $calc['option_id'] ?? null;

                    $option = $optionId !== null
                        ? $activity->options->firstWhere('id', (int) $optionId)
                        : null;

                    if ($option !== null) {
                        $factor = (float) ($option->emissionFactor?->ef_value ?? 0);
                        $unit = $option->activityUnit?->unit_name ?? $activity->activityUnit?->unit_name ?? '–';
                        $source = $option->emissionFactor?->source;
                        $optionName = $option->description;
                    } else {
                        $factor = (float) ($activity->emissionFactor?->ef_value ?? 0);
                        $unit = $activity->activityUnit?->unit_name ?? '–';
                        $source = $activity->emissionFactor?->source;
                        $optionName = null;
                    }

                    $isHeritage = $activity->emissionFactor === null && $activity->options->isEmpty();

                    $enriched->push([
                        'activity_id' => $activity->getKey(),
                        'activity' => $activity->description,
                        'subprocess' => $subprocess->description,
                        'process' => $process->description,
                        'scheme' => $process->scheme?->description,
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
                    $processTotal += $emission;
                    $processCount++;

                    if ($quantity > 0) {
                        $processFilled++;
                    }
                }

                $stages->push([
                    'process' => $process->description,
                    'scheme' => $process->scheme?->description,
                    'name' => $subprocess->description,
                    'total' => $stageTotal,
                    'count' => $stageCount,
                ]);
            }
        }

        $total = (float) ($calculation['total'] ?? 0);
        $filled = $enriched->where(fn ($row): bool => (float) $row['quantity'] > 0)->count();

        $processSummaries = $inventory->map(function (Process $process) use ($enriched): array {
            $rows = $enriched->where(fn ($row): bool => $row['process'] === $process->description);
            $processTotal = (float) $rows->sum('emission');

            return [
                'id' => $process->getKey(),
                'scheme' => $process->scheme?->description,
                'description' => $process->description,
                'total' => $processTotal,
                'count' => $rows->count(),
                'filled' => $rows->where(fn ($row): bool => (float) $row['quantity'] > 0)->count(),
            ];
        })->values();

        $hotspot = $enriched->sortByDesc('emission')->first();
        $sources = $inventory
            ->flatMap(fn (Process $process) => $process->subprocesses)
            ->flatMap(fn ($subprocess) => $subprocess->activities)
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
            'processes' => $processSummaries,
            'stages' => $stages,
            'items' => $enriched,
            'total' => $total,
            'filled' => $filled,
            'totalActivities' => $enriched->count(),
            'hotspot' => $hotspot,
            'sources' => $sources,
        ];
    }

    /**
     * Build the payload for UpsertReport from the wizard session.
     *
     * @return array{scheme_id: int, process_id: int, title: string, slug: null, summary: null, is_published: true, items: array}
     */
    public function buildStorePayload(): array
    {
        $ordered = $this->orderedProcesses();

        if ($ordered->isEmpty()) {
            abort(422, 'Belum ada proses untuk disimpan.');
        }

        $state = $this->state() ?? ['current' => 0, 'entries' => []];
        $entries = $state['entries'] ?? [];

        /** @var Process $last */
        $last = $ordered->last();

        $activityIds = Activity::query()
            ->whereHas('subprocess', fn ($query) => $query->whereIn('process_id', $ordered->pluck('id')))
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
            'process_id' => (int) $last->getKey(),
            'title' => $title,
            'slug' => null,
            'summary' => null,
            'is_published' => true,
            'items' => $items,
        ];
    }

    public function activityIdsForProcess(Process $process): array
    {
        return Activity::query()
            ->whereHas('subprocess', fn ($query) => $query->where('process_id', $process->getKey()))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function assertProcessInScope(int $processId): bool
    {
        return $this->orderedProcesses()->contains('id', $processId);
    }

    public function optionBelongsToActivity(int $activityId, int $optionId): bool
    {
        return DB::table('activity_option')
            ->where('activity_id', $activityId)
            ->where('option_id', $optionId)
            ->exists();
    }
}
