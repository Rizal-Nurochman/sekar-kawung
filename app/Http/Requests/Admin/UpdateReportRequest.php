<?php

namespace App\Http\Requests\Admin;

use App\Models\SustainabilityReport;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Only the editorial fields of an existing report are editable here. The
 * scheme and system boundary come from the calculator run that created the
 * report and are never accepted from the request.
 */
class UpdateReportRequest extends FormRequest
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
        $routeValue = $this->route('report');
        $reportId = $routeValue instanceof SustainabilityReport ? $routeValue->getKey() : $routeValue;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'alpha_dash', 'max:255', Rule::unique('sustainability_reports', 'slug')->ignore($reportId)],
            'summary' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
