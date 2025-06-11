<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportReportsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'required|integer|exists:reports,id'
        ];
    }

    public function messages(): array
    {
        return [
            'report_ids.required' => 'Report IDs are required',
            'report_ids.array' => 'Report IDs must be an array',
            'report_ids.min' => 'At least one report ID is required',
            'report_ids.*.integer' => 'Each report ID must be an integer',
            'report_ids.*.exists' => 'One or more report IDs do not exist'
        ];
    }
}
