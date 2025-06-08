<?php

namespace App\Http\Requests;

use App\Repositories\MtnSiteRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMtnSiteRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:mtn_sites,code'],
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
            'name.required' => 'The site name is required',
            'code.required' => 'The site code is required',
            'code.unique' => 'كود الموقع موجود مسبقا',
        ];
    }
}
