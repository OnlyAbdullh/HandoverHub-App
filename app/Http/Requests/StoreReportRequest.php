<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'report.generator_id'       => 'required|integer|exists:generators,id',
            'report.mtn_site_id'        => 'sometimes|integer|exists:mtn_sites,id',
            'report.visit_type'         => 'sometimes|string|max:50',
            'report.report_number'      => 'sometimes|string|unique:reports,report_number',
            'report.visit_date'         => 'sometimes|date',
            'report.visit_time'         => 'sometimes|date_format:H:i:s',
            'report.current_reading'    => 'sometimes|numeric',
            'report.ats_status'        => 'sometimes|boolean|max:20',
            'report.oil_pressure'       => 'sometimes|numeric',
            'report.temperature'        => 'sometimes|numeric',
            'report.burned_oil_quantity'=> 'sometimes|numeric',
            'report.battery_voltage'    => 'sometimes|numeric',
            'report.frequency'          => 'sometimes|numeric',
            'report.voltage_L1'         => 'sometimes|numeric',
            'report.voltage_L2'         => 'sometimes|numeric',
            'report.voltage_L3'         => 'sometimes|numeric',
            'report.load_L1'            => 'sometimes|numeric',
            'report.load_L2'            => 'sometimes|numeric',
            'report.load_L3'            => 'sometimes|numeric',
            'report.oil_quantity'       => 'sometimes|numeric',
            'report.visit_reason'       => 'sometimes|string|max:100',
            'report.technical_status'   => 'sometimes|string|max:50',
            'report.longitude'          => 'sometimes|string|max:50',
            'report.latitude'           => 'sometimes|string|max:50',
            'parts_used'                => 'sometimes|array|min:1',
            'parts_used.*.part_id'      => 'sometimes|integer|exists:parts,id',
            'parts_used.*.quantity'     => 'sometimes|integer|min:1',
            'parts_used.*.notes'        => 'nullable|string',
            'parts_used.*.is_faulty'    => 'sometimes|boolean',
            'parts_used.*.faulty_quantity'    => 'sometimes|integer|min:1',

            'completed_task'            => 'sometimes|array|min:1',
            'completed_task.*'          => 'sometimes|string',

            'technician_notes'          => 'sometimes|array|min:1',
            'technician_notes.*'        => 'sometimes|string',
        ];
    }

}
