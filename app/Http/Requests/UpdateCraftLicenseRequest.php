<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCraftLicenseRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status'              => [
                'required',
                'string',
                Rule::in(['new', 'renewal', 'renewal_pending', 'undefined', 'stopped', 'closed', 'active_no_file']),
                function ($attribute, $value, $fail) {
                    /** @var \App\Models\CraftLicense|null $license */
                    $license = $this->route('craft_license');
                    if ($license && in_array($value, ['closed', 'stopped']) && ($license->remaining ?? 0) > 0) {
                        $fail('لا يمكن تغيير حالة الحرفة إلى مغلقة أو متوقفة عن العمل وهناك مبلغ متبقي غير مدفوع.');
                    }
                },
            ],
            'tax_number'          => 'nullable|string|max:50',
            'last_license'        => 'nullable|string|max:100',
            'id_number'           => 'nullable|string|size:9',
            'phone_number'        => 'nullable|string|max:20',
            'owner_name'          => 'nullable|string|max:255',
            'property_owner'      => 'nullable|string|max:255',
            'property_owner_id'   => 'nullable|string|size:9',
            'ownership_entity'    => 'nullable|string|max:255',
            'first_license_year'  => 'nullable|integer|min:1900|max:2100',
            'classification'      => 'required|string',
            'craft_type'          => 'required|string',
            'detailed_craft_type' => 'required|string|max:255',
            'registration_date'   => 'required|date',
            'last_payment_date'   => 'nullable|date',
            'holiday_days'        => 'nullable|string|max:255',
            'licensed_operator'   => 'nullable|string|max:255',
            'is_licensed'         => 'nullable|boolean',
            'commercial_name'     => 'nullable|string|max:255',
            'neighborhood'        => 'nullable|string|max:255',
            'main_street'         => 'nullable|string|max:255',
            'address'             => 'nullable|string|max:500',
            'nearest_mosque'      => 'nullable|string|max:255',
            'gis_building_number' => 'nullable|string|max:50',
            'street_number'       => 'nullable|string|max:50',
            'floor_number'        => 'nullable|string|max:20',
            'unit_number'         => 'nullable|string|max:20',
            // نسمح بأن يكون إجمالي الديون بأي قيمة (حتى لو سالبة لرصيد لصالح الحرفة)
            'total_debts'         => 'nullable|numeric',
            'craft_fees'          => 'required|numeric|min:0',
            'payment_amount'      => 'nullable|numeric|min:0',
            'signboard_fees'      => 'nullable|numeric|min:0',
            'discount_percent'    => 'nullable|numeric|min:0|max:100',
            'remaining_years'     => 'nullable|integer|min:0',
            'advertisement_board' => 'nullable|string|max:255',
            'number_of_stores'    => 'nullable|integer|min:0',
            'board_in_meters'     => 'nullable|numeric|min:0',
            'final_price'         => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:1000',
        ];
    }
}
