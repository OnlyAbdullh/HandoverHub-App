<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'code'         => ['sometimes','nullable', 'string', 'max:255', 'unique:parts,code'],
            'is_general'   => ['required', 'boolean'],

            'engine_ids'   => [
                'array',
                Rule::prohibitedIf($this->boolean('is_general')),
            ],

            'engine_ids.*' => [
                'exists:engines,id',
                Rule::prohibitedIf($this->boolean('is_general')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'The name of the part is required.',
            'code.unique'                => 'The code of the part already exists.',
            'is_general.required'        => 'Please specify whether the part is general or not.',

            'engine_ids.prohibited'      => 'You cannot select engines when the part is marked as general.',
            'engine_ids.*.prohibited'    => 'You cannot select engines when the part is marked as general.',
            'engine_ids.*.exists'        => 'One of the selected engines does not exist.',
        ];
    }
}
