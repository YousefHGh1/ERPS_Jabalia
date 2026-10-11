<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImprisonmentOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('notice_id') && $this->has('order_number')) {
            return [
                'notice_id' => 'required|exists:notices,id',
                'order_number' => 'required',
                'order_date' => 'required|date_format:Y-m-d H:i:s',
                'notes' => 'nullable|string',
            ];
        }

        return [
            'order_number' => 'required|string|unique:imprisonment_orders,order_number',
            'order_date' => 'required|date',
            'court_name' => 'required|string',
            'judge_name' => 'required|string',
            'defendant_name' => 'required|string',
            'defendant_identity' => 'required|string',
            'imprisonment_period' => 'required|string',
            'imprisonment_type' => 'required|string',
            'case_number' => 'required|string',
            'case_year' => 'required|integer',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function messages(): array
    {
        if ($this->has('notice_id') && $this->has('order_number')) {
            return [
                'notice_id.required' => 'يرجى اختيار الإخطار',
                'notice_id.exists' => 'الإخطار غير صحيح',
                'order_number.required' => 'يرجى إدخال رقم أمر الحبس',
                'order_date.required' => 'يرجى إدخال تاريخ ووقت أمر الحبس',
                'order_date.date_format' => 'صيغة التاريخ غير صحيحة',
            ];
        }
        return [];
    }
}
