<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignGeneratorsRequest extends FormRequest
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
            'generator_ids' => ['required', 'array', 'min:1'],
            'generator_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('generators', 'id')->whereNull('mtn_site_id'),
            ],
        ];
    }

    /**
     * تسحب مصفوفة المعرفات بعد التحقّق
     */
    public function getGeneratorIds(): array
    {
        return $this->input('generator_ids', []);
    }
}
