<?php

namespace App\Actions\Admin;

use App\Models\Activity;
use App\Models\Option;

class CalculateEmissions
{
    /**
     * Calculate per-item emissions and the total.
     *
     * @param  array{items?: array<int, array{activity_id: int|string, option_id?: int|string|null, quantity?: float|int|string|null}>}  $validated
     * @return array{items: array<int, array{activity_id: int, option_id: ?int, quantity: float, emission_result: float}>, total: float}
     */
    public function handle(array $validated): array
    {
        $rows = $validated['items'] ?? [];

        if (! is_array($rows) || $rows === []) {
            return ['items' => [], 'total' => 0.0];
        }

        $activityIds = collect($rows)
            ->pluck('activity_id')
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->map(fn ($value): int => (int) $value)
            ->unique()
            ->values();

        $optionIds = collect($rows)
            ->pluck('option_id')
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->map(fn ($value): int => (int) $value)
            ->unique()
            ->values();

        $activities = Activity::query()
            ->with('emissionFactor')
            ->whereIn('id', $activityIds)
            ->get()
            ->keyBy('id');

        $options = Option::query()
            ->with('emissionFactor')
            ->whereIn('id', $optionIds)
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0.0;

        foreach ($rows as $row) {
            if (! is_array($row) || ! isset($row['activity_id']) || $row['activity_id'] === '') {
                continue;
            }

            $activityId = (int) $row['activity_id'];
            $optionId = isset($row['option_id']) && $row['option_id'] !== null && $row['option_id'] !== ''
                ? (int) $row['option_id']
                : null;
            $quantity = isset($row['quantity']) && $row['quantity'] !== null && $row['quantity'] !== ''
                ? (float) $row['quantity']
                : 0.0;

            $activity = $activities->get($activityId);

            if ($optionId !== null) {
                $factor = (float) ($options->get($optionId)?->emissionFactor?->ef_value ?? 0);
            } else {
                $factor = (float) ($activity?->emissionFactor?->ef_value ?? 0);
            }

            $emission = $quantity * $factor;
            $total += $emission;

            $items[] = [
                'activity_id' => $activityId,
                'option_id' => $optionId,
                'quantity' => $quantity,
                'emission_result' => $emission,
            ];
        }

        return ['items' => $items, 'total' => $total];
    }
}
