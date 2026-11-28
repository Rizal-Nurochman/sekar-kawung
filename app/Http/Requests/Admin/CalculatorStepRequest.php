<?php

namespace App\Http\Requests\Admin;

use App\Models\Activity;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class CalculatorStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'boundary_id' => ['required', 'integer', 'exists:boundaries,id'],
            'direction' => ['required', 'string', 'in:next,back'],
            'items' => ['present', 'array'],
            'items.*.activity_id' => ['required', 'integer', 'exists:activities,id'],
            'items.*.option_id' => ['nullable', 'integer', 'exists:options,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('boundary_id')) {
                    return;
                }

                $data = $validator->getData();
                $boundaryId = (int) ($data['boundary_id'] ?? 0);
                $items = $data['items'] ?? [];

                if (! is_array($items) || $items === []) {
                    return;
                }

                $activityIds = collect($items)
                    ->pluck('activity_id')
                    ->filter(fn ($value): bool => $value !== null && $value !== '')
                    ->map(fn ($value): int => (int) $value)
                    ->unique()
                    ->values();

                if ($activityIds->isEmpty()) {
                    return;
                }

                $scoped = Activity::query()
                    ->whereIn('id', $activityIds)
                    ->whereHas('process', fn ($query) => $query->where('boundary_id', $boundaryId))
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->all();

                $allowed = array_flip($scoped);

                foreach ($items as $index => $item) {
                    if (! is_array($item) || ! isset($item['activity_id']) || $item['activity_id'] === '') {
                        continue;
                    }

                    $activityId = (int) $item['activity_id'];

                    if (! isset($allowed[$activityId])) {
                        $validator->errors()->add(
                            "items.{$index}.activity_id",
                            'Aktivitas tidak termasuk dalam boundary ini.'
                        );
                    }
                }

                $pairs = [];
                foreach ($items as $index => $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $activityId = $item['activity_id'] ?? null;
                    $optionId = $item['option_id'] ?? null;

                    if ($optionId === null || $optionId === '') {
                        continue;
                    }

                    if ($activityId === null || $activityId === '') {
                        continue;
                    }

                    $pairs[$index] = [(int) $activityId, (int) $optionId];
                }

                if ($pairs === []) {
                    return;
                }

                $links = DB::table('activity_option')
                    ->whereIn('activity_id', array_column($pairs, 0))
                    ->whereIn('option_id', array_column($pairs, 1))
                    ->get(['activity_id', 'option_id']);

                $valid = [];
                foreach ($links as $link) {
                    $valid[$link->activity_id.':'.$link->option_id] = true;
                }

                foreach ($pairs as $index => [$activityId, $optionId]) {
                    if (! isset($valid[$activityId.':'.$optionId])) {
                        $validator->errors()->add(
                            "items.{$index}.option_id",
                            'Opsi yang dipilih tidak tersedia untuk aktivitas ini.'
                        );
                    }
                }
            },
        ];
    }
}
