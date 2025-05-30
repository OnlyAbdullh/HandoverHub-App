<?php

namespace App\Http\Requests;

use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
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
            'name' => 'required|string|max:30|unique:brands,name',
            'type' => [
                'required',
                'string',
                Rule::in([Brand::TYPE_GENERATOR, Brand::TYPE_ENGINE])
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The brand name is required.',
            'name.string' => 'The brand name must be a string.',
            'name.max' => 'The brand name may not be greater than 30 characters.',
            'name.unique' => 'A brand with this name already exists.',
            'type.required' => 'The brand type is required.',
            'type.string' => 'The brand type must be a string.',
            'type.in' => 'The brand type must be either generator or engine.',
        ];
    }
}
