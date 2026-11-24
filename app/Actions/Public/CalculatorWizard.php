<?php

namespace App\Actions\Public;

use App\Actions\Admin\CalculateEmissions;
use App\Models\Activity;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class CalculatorWizard
{
    public const SESSION_KEY = 'public_calculator_wizard';

    public function __construct(protected CalculateEmissions $calculator) {}

    /**
     * All products in wizard order: scheme sort, then product sort.
     *
     * @return Collection<int, Product>
     */
    public function orderedProducts(): Collection
    {
        return Product::query()
            ->with('scheme')
            ->join('schemes', 'schemes.id', '=', 'products.scheme_id')
            ->orderBy('schemes.sort_order')
            ->orderBy('schemes.id')
            ->orderBy('products.sort_order')
            ->orderBy('products.id')
            ->select('products.*')
            ->get();
    }

    /**
     * Load one product with its full inventory, ordered.
     */
    public function productWithInventory(Product $product): Product
    {
        return Product::query()
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
            ->findOrFail($product->getKey());
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
    public function saveStep(Product $product, array $items): void
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
     * Totals for saved entries outside the given product.
     *
     * @param  array<string, array{option_id: ?int, quantity: float}>  $entries
     * @return array{total: float, filled: int}
     */
    public function baseTotals(Product $currentProduct, array $entries): array
    {
        $currentActivityIds = $currentProduct->processes
            ->flatMap(fn ($process) => $process->activities->pluck('id'))
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
            ->whereHas('process.product')
            ->count();
    }

    /**
     * Enriched review data across all products.
     *
     * @param  array<string, array{option_id: ?int, quantity: float}>  $entries
     * @return array{
     *     products: Collection,
     *     boundaries: Collection,
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
        $ordered = $this->orderedProducts();
        $state = $this->state();
        $entries = $state['entries'] ?? [];

        $productIds = $ordered->pluck('id')->all();

        $inventory = Product::query()
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
            ->whereIn('id', $productIds)
            ->get()
            ->sortBy([
                fn ($a, $b): int => [$a->scheme->sort_order, $a->scheme->id, $a->sort_order, $a->id] <=> [$b->scheme->sort_order, $b->scheme->id, $b->sort_order, $b->id],
            ])
            ->values();

        $allItems = [];
        foreach ($inventory as $product) {
            foreach ($product->processes as $process) {
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

        foreach ($inventory as $product) {
            $productTotal = 0.0;
            $productFilled = 0;
            $productCount = 0;

            foreach ($product->processes as $process) {
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
                        'product' => $product->description,
                        'scheme' => $product->scheme?->description,
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
                    $productTotal += $emission;
                    $productCount++;

                    if ($quantity > 0) {
                        $productFilled++;
                    }
                }

                $stages->push([
                    'product' => $product->description,
                    'scheme' => $product->scheme?->description,
                    'name' => $process->description,
                    'total' => $stageTotal,
                    'count' => $stageCount,
                ]);
            }
        }

        $total = (float) ($calculation['total'] ?? 0);
        $filled = $enriched->where(fn ($row): bool => (float) $row['quantity'] > 0)->count();

        $productSummaries = $inventory->map(function (Product $product) use ($enriched): array {
            $rows = $enriched->where(fn ($row): bool => $row['product'] === $product->description);
            $productTotal = (float) $rows->sum('emission');

            return [
                'id' => $product->getKey(),
                'scheme' => $product->scheme?->description,
                'description' => $product->description,
                'total' => $productTotal,
                'count' => $rows->count(),
                'filled' => $rows->where(fn ($row): bool => (float) $row['quantity'] > 0)->count(),
            ];
        })->values();

        // Per-boundary cumulative scheme subtotals: within one scheme,
        // boundary 2 includes boundary 1 (PRD progressive Cradle-to-Gate).
        $cumulativeByScheme = [];
        $boundarySections = $inventory->map(function (Product $product) use ($enriched, $stages, $total, &$cumulativeByScheme): array {
            $rows = $enriched->where(fn ($row): bool => $row['product'] === $product->description);
            $productTotal = (float) $rows->sum('emission');
            $schemeKey = $product->scheme?->description ?? 'Tanpa skema';
            $cumulativeByScheme[$schemeKey] = ($cumulativeByScheme[$schemeKey] ?? 0.0) + $productTotal;

            return [
                'id' => $product->getKey(),
                'scheme' => $product->scheme?->description,
                'description' => $product->description,
                'total' => $productTotal,
                'cumulative' => (float) $cumulativeByScheme[$schemeKey],
                'share' => $total > 0 ? $productTotal / $total * 100 : 0.0,
                'count' => $rows->count(),
                'filled' => $rows->where(fn ($row): bool => (float) $row['quantity'] > 0)->count(),
                'stages' => $stages->where(fn ($stage): bool => $stage['product'] === $product->description)->values(),
                'items' => $rows->values(),
            ];
        })->values();

        $hotspot = $enriched->sortByDesc('emission')->first();
        $sources = $inventory
            ->flatMap(fn (Product $product) => $product->processes)
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
            'products' => $productSummaries,
            'boundaries' => $boundarySections,
            'stages' => $stages,
            'items' => $enriched,
            'total' => $total,
            'filled' => $filled,
            'totalActivities' => $enriched->count(),
            'hotspot' => $hotspot,
            'sources' => $sources,
        ];
    }
}
