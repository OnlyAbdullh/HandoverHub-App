<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:parts,code',
            'is_general' => ['required', 'boolean'],
            'note' => 'nullable|string|max:1000',
            'engine_ids' => 'array',
            'engine_ids.*' => 'exists:engines,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'the name of the part is required',
            'code.required' => 'the code of the part is required',
            'code.unique' => 'the code of the part is already exist',
            'engine_ids.*.exists' => 'one of the Engines is not exist'
        ];
    }
}
