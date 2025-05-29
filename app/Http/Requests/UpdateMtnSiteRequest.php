<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMtnSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('mtn_sites', 'code')->ignore($this->route('mtn_site')),
            ],
            'longitude' => ['sometimes', 'string', 'max:50'],
            'latitude' => ['sometimes', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.string' => 'The site name must be a string',
            'code.unique' => 'This site code is already in use',
            'longitude.string' => 'The longitude must be a string',
            'latitude.string' => 'The latitude must be a string',
        ];
    }
}
