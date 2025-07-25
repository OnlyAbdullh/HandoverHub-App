<?php

namespace App\Http\Requests\UpdateRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:255|unique:parts,code,' . $this->part,
            'is_general' => 'sometimes|boolean',
            /*       'engine_ids' => 'array',
                   'engine_ids.*' => 'exists:engines,id'*/
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'كود القطعة موجود مسبقاً',
            // 'engine_ids.*.exists' => 'أحد المحركات المحددة غير موجود'
        ];
    }
}
