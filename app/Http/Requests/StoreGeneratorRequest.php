<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneratorRequest extends FormRequest
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
            'mtn_site_id'   => 'nullable|integer|exists:mtn_sites,id',
            'engine_id'     => 'required|integer|exists:engines,id',
            'brand_id'      => [
                'required',
                'integer',
                'exists:brands,id,type,generator'
            ],
            'initial_meter' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'engine_id.required' => 'Engine is required',
            'engine_id.exists' => 'Selected engine does not exist',
            'brand_id.required' => 'Brand is required',
            'brand_id.exists' => 'Selected brand does not exist or is not a generator brand',
            'site_id.exists' => 'Selected site does not exist',
            'initial_meter.numeric' => 'Initial meter must be a number',
            'initial_meter.min' => 'Initial meter cannot be negative',
        ];
    }
}
