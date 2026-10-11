<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEngineeringRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'file_number' => 'required|string|max:50',
            'file_year' => 'required|string|max:10',
            'first_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'grandfather_name' => 'nullable|string|max:100',
            'family_name' => 'required|string|max:100',
            'national_id' => 'required|string|max:50',
            'phone_number' => 'nullable|string|max:20',
            'mobile_number' => 'nullable|string|max:20',
            'neighborhood' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'plot_number' => 'nullable|integer',
            'parcel_number' => 'nullable|integer',
            'division_number' => 'nullable|integer',
            'area_size' => 'nullable|numeric',
            'building_type' => 'required|string|max:100',
            'floors_count' => 'required|integer|min:0',
            'apartments_count' => 'required|integer|min:0',
            'gis_fees' => 'required|numeric|min:0',
            'license_fees' => 'required|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'remaining_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'agreement' => 'boolean',
        ];
    }
}
