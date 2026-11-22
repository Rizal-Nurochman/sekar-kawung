<?php

namespace App\Http\Requests\Admin;

use App\Models\ActivityUnit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityUnitRequest extends FormRequest
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
        $routeParam = $this->route('activity_unit') ?? $this->route('activityUnit');
        $ignoreId = $routeParam instanceof ActivityUnit ? $routeParam->getKey() : $routeParam;

        return [
            'unit_name' => ['required', 'string', 'max:255', Rule::unique('activity_units', 'unit_name')->ignore($ignoreId)],
        ];
    }
}
