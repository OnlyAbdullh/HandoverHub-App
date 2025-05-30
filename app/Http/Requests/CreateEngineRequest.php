<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEngineRequest extends FormRequest
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
            'brand_id' => [
                'required',
                'integer',
                'exists:brands,id',
                Rule::exists('brands', 'id')->where(function ($query) {
                    return $query->where('type', 'engine');
                }),
            ],
            'capacity_id' => [
                'required',
                'integer',
                'exists:capacities,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.required' => 'Brand ID is required',
            'brand_id.integer' => 'Brand ID must be an integer',
            'brand_id.exists' => 'Selected brand does not exist or is not of type "engine"',
            'capacity_id.required' => 'Capacity ID is required',
            'capacity_id.integer' => 'Capacity ID must be an integer',
            'capacity_id.exists' => 'Selected capacity does not exist',
        ];
    }
}
