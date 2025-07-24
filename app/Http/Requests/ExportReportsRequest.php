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
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required'            => 'يرجى تحديد تاريخ البداية.',
            'end_date.required'              => 'يرجى تحديد تاريخ النهاية.',
            'start_date.before_or_equal'     => 'تاريخ البداية يجب أن يكون قبل أو يساوي تاريخ النهاية.',
            'end_date.after_or_equal'        => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية.',
        ];
    }
}
