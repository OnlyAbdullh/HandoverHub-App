<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportPartRequest extends FormRequest
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
            'part_id' => 'required|integer|exists:parts,id',
            'quantity' => 'sometimes|integer|min:1',
            'is_faulty' => 'required|boolean',
            'notes' => 'sometimes|nullable|string|max:500',
            'faulty_quantity' => 'sometimes|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'part_id.required' => 'The part ID is required.',
            'part_id.exists' => 'The selected part does not exist.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
            'faulty_quantity.integer' => 'The quantity must be an integer.',
            'faulty_quantity.min' => 'The quantity must be at least 1.',
            'notes.max' => 'The notes may not be greater than 500 characters.',
        ];
    }
}
