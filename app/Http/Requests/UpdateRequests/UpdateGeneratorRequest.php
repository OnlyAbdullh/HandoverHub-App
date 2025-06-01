<?php

namespace App\Http\Requests\UpdateRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneratorRequest extends FormRequest
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
            'mtn_site_id' => 'sometimes|nullable|integer|exists:mtn_sites,id',
            'initial_meter' => 'sometimes|nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'mtn_site_id.exists' => 'Selected site does not exist',
            'initial_meter.numeric' => 'Initial meter must be a number',
            'initial_meter.min' => 'Initial meter cannot be negative',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if at least one field is being updated
            $fields = ['mtn_site_id', 'initial_meter'];
            $hasUpdate = false;

            foreach ($fields as $field) {
                if ($this->has($field)) {
                    $hasUpdate = true;
                    break;
                }
            }

            if (!$hasUpdate) {
                $validator->errors()->add('general', 'At least one field must be provided for update');
            }
        });
    }
}
