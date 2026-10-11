<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('year') && $this->has('notice_id')) {
            return [
                'year' => 'required|integer',
                'notice_id' => 'required|integer',
                'notice_month' => 'required',
                'notice_reason_id' => 'required|exists:notice_reasons,id',
                'fine_amount' => 'nullable|numeric',
                'jebaia_no' => 'nullable|numeric',
                'notice_date' => 'required|date_format:Y-m-d H:i:s',
                'rent_location_id' => 'nullable|exists:rent_locations,id',
                'build_desc' => 'nullable|string|max:255',
                'rent_no' => 'nullable|integer',
                'notice_address' => 'nullable|string|max:255',
                'org_file_number' => 'nullable|string|max:255',
                'user_id' => 'nullable|exists:users,id',
            ];
        }

        return [
            'notice_number' => 'required|string|unique:notices,notice_number',
            'notice_date' => 'required|date',
            'notice_reason_id' => 'required|exists:notice_reasons,id',
            'rent_location_id' => 'nullable|exists:rent_locations,id',
            'description' => 'nullable|string',
            'identity_number' => 'nullable|string|max:50',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function messages(): array
    {
        if ($this->has('year') && $this->has('notice_id')) {
            return [
                'year.required' => 'يرجى اختيار سنة الإخطار',
                'notice_id.required' => 'يرجى إدخال رقم الإخطار',
                'notice_month.required' => 'يرجى اختيار شهر الإخطار',
                'notice_reason_id.required' => 'يرجى اختيار سبب الإخطار',
                'notice_reason_id.exists' => 'سبب الإخطار غير صحيح',
                'rent_location_id.exists' => 'نوع السوق غير صحيح',
                'notice_date.required' => 'يرجى إدخال تاريخ ووقت الإخطار',
                'notice_date.date_format' => 'صيغة تاريخ الإخطار غير صحيحة',
            ];
        }
        return [];
    }
}
