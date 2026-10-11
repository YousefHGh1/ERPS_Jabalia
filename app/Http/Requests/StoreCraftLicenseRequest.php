<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCraftLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_number'         => ['required', 'string', 'max:50', Rule::unique('craft_licenses', 'file_number')->whereNull('deleted_at')],
            'owner_name'          => ['required', 'string', 'max:255'],
            'id_number'           => ['nullable', 'string', 'max:9'],
            'phone_number'        => ['nullable', 'string', 'max:20'],
            'tax_number'          => ['nullable', 'string', 'max:50'],
            'last_license'        => ['nullable', 'string', 'max:100'],
            'property_owner'      => ['nullable', 'string', 'max:255'],
            'property_owner_id'   => ['nullable', 'string', 'max:9'],
            'ownership_entity'    => ['nullable', 'string', 'max:255'],
            'first_license_year'  => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'craft_type'          => ['nullable', 'string', 'max:255'],
            'classification'      => ['required', 'string', 'max:255'],
            'detailed_craft_type' => ['required', 'string', 'max:255'],
            'registration_date'   => ['required', 'date'],
            'last_payment_date'   => ['nullable', 'date'],
            'holiday_days'        => ['nullable', 'string', 'max:255'],
            'licensed_operator'   => ['nullable', 'string', 'max:255'],
            'is_licensed'         => ['nullable', 'boolean'],
            'commercial_name'     => ['nullable', 'string', 'max:255'],
            'neighborhood'        => ['nullable', 'string', 'max:255'],
            'main_street'         => ['nullable', 'string', 'max:255'],
            'address'             => ['nullable', 'string', 'max:500'],
            'nearest_mosque'      => ['nullable', 'string', 'max:255'],
            'gis_building_number' => ['nullable', 'string', 'max:50'],
            'street_number'       => ['nullable', 'string', 'max:50'],
            'floor_number'        => ['nullable', 'string', 'max:20'],
            'unit_number'         => ['nullable', 'string', 'max:20'],
            'total_debts'         => ['nullable', 'numeric'],
            'craft_fees'          => ['required', 'numeric', 'min:0'],
            'payment_amount'      => ['nullable', 'numeric', 'min:0'],
            'signboard_fees'      => ['nullable', 'numeric', 'min:0'],
            'discount_percent'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'remaining_years'     => ['nullable', 'integer', 'min:0'],
            'advertisement_board' => ['nullable', 'string', 'max:255'],
            'number_of_stores'    => ['nullable', 'integer', 'min:0'],
            'board_in_meters'     => ['nullable', 'numeric', 'min:0'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'status'              => ['required', 'string', Rule::in(['new', 'renewal', 'undefined', 'stopped', 'closed', 'active_no_file'])],
        ];
    }

    public function messages(): array
    {
        return [
            'file_number.required'       => __('رقم الملف مطلوب'),
            'file_number.unique'         => __('رقم الملف موجود بالفعل، يرجى إدخال رقم آخر'),
            'owner_name.required'        => __('اسم المالك مطلوب'),
            'craft_type.required'        => __('نوع الحرفة مطلوب'),
            'craft_type.*'               => '',
            'classification.required'    => __('تصنيف الحرفة مطلوب'),
            'registration_date.required' => __('تاريخ التسجيل مطلوب'),
            'craft_fees.required'        => __('رسوم الحرفة مطلوبة'),
        ];
    }
}
