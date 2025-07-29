<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreReportRequest extends FormRequest
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
        $isCreate = $this->isMethod('post');
        $allowedReasons = ['بدل مسروق', 'بدل عاطل', 'إضافة', 'لا يوجد عاطل'];
        return [
            'report.generator_id'            => 'required|integer|exists:generators,id',
            'report.mtn_site_id'             => 'sometimes|nullable|integer|exists:mtn_sites,id',
            'report.visit_type'              => 'sometimes|nullable|string|max:50',
            'report.report_number'           => 'sometimes|nullable|string|unique:reports,report_number',
            'report.visit_date'              => 'sometimes|nullable|date',
            'report.visit_time'              => 'sometimes|nullable|date_format:H:i:s',
            'report.current_reading'         => 'sometimes|nullable|numeric',
            'report.ats_status'              => 'sometimes|nullable|boolean|max:20',
            'report.oil_pressure'            => 'sometimes|nullable|numeric',
            'report.temperature'             => 'sometimes|nullable|numeric',
            'report.burned_oil_quantity'     => 'sometimes|nullable|numeric',
            'report.battery_voltage'         => 'sometimes|nullable|numeric',
            'report.frequency'               => 'sometimes|nullable|numeric',
            'report.voltage_L1'              => 'sometimes|nullable|numeric',
            'report.voltage_L2'              => 'sometimes|nullable|numeric',
            'report.voltage_L3'              => 'sometimes|nullable|numeric',
            'report.load_L1'                 => 'sometimes|nullable|numeric',
            'report.load_L2'                 => 'sometimes|nullable|numeric',
            'report.load_L3'                 => 'sometimes|nullable|numeric',
            'report.oil_quantity'            => 'sometimes|nullable|numeric',
            'report.visit_reason'            => 'sometimes|nullable|string|max:100',
            'report.technical_status'        => 'sometimes|nullable|string|max:50',
            'report.longitude'               => 'sometimes|nullable|string|max:50',
            'report.latitude'                => 'sometimes|nullable|string|max:50',

            'parts_used'                     => 'sometimes|array|min:1',
            'parts_used.*.part_id'           => 'sometimes|nullable|integer|exists:parts,id',
            'parts_used.*.quantity'          => 'sometimes|nullable|integer|min:0',
            'parts_used.*.notes'             => 'nullable|string',
            'parts_used.*.is_faulty'         => 'sometimes|nullable|boolean',
            'parts_used.*.faulty_quantity'   => 'sometimes|nullable|integer|min:1',
            'parts_used.*.reason' => [
                $isCreate ? 'required' : 'sometimes',
                'string',
                Rule::in($allowedReasons),
            ],

            'completed_task'                 => 'sometimes|array',
            'completed_task.*'               => 'sometimes|nullable|string',

            'technician_notes'              => 'sometimes|array',
            'technician_notes.*'            => 'sometimes|nullable|string',
        ];
    }

}
