<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
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
            'subprocess_id' => ['required', 'integer', 'exists:subprocesses,id'],
            'activity_unit_id' => ['nullable', 'integer', 'exists:activity_units,id'],
            'emission_factor_id' => ['nullable', 'integer', 'exists:emission_factors,id'],
            'description' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}
