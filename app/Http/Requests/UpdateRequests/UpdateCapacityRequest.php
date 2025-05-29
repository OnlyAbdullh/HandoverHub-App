<?php

namespace App\Http\Requests\UpdateRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCapacityRequest extends FormRequest
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
        $capacityId = $this->route('capacity');

        return [
            'value' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('capacities', 'value')->ignore($capacityId)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'value.required' => 'The capacity value is required.',
            'value.integer' => 'The capacity value must be an integer.',
            'value.min' => 'The capacity value must be at least 1.',
            'value.unique' => 'A capacity with this value already exists.'
        ];
    }
}
