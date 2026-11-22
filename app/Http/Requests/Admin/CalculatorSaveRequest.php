<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CalculatorSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'scheme_id' => ['required', 'integer', 'exists:schemes,id'],
            'process_id' => ['required', 'integer', 'exists:processes,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'max:255', Rule::unique('sustainability_reports', 'slug')->ignore($this->route('report'))],
            'summary' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'items' => ['required', 'array'],
            'items.*.activity_id' => ['required', 'integer', 'exists:activities,id'],
            'items.*.option_id' => ['nullable', 'integer', 'exists:options,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Validate that options belong to their activity and the process belongs to the scheme.
     *
     * @param  array<string, mixed>  $data
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $data = $validator->getData();

                $schemeId = $data['scheme_id'] ?? null;
                $processId = $data['process_id'] ?? null;

                if ($schemeId !== null && $processId !== null) {
                    $belongs = DB::table('processes')
                        ->where('id', $processId)
                        ->where('scheme_id', $schemeId)
                        ->exists();

                    if (! $belongs) {
                        $validator->errors()->add('process_id', 'Proses yang dipilih tidak termasuk dalam skema yang dipilih.');
                    }
                }

                $items = $data['items'] ?? [];

                if (! is_array($items) || $items === []) {
                    return;
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
