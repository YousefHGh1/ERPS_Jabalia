<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestMailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * يسمح للموظفين التابعين للأقسام أو الدوائر بتحديث حالة الطلبات المخصصة لوحدتهم
     */
    public function authorize(): bool
    {
        $user = $this->user();
        // الموظفون التابعون للأقسام/الدوائر يمكنهم تعديل حالة الطلبات المخصصة لوحدتهم (لا يتطلب صلاحيات)
        if ($user->employee && ($user->employee->section_id !== null || $user->employee->department_id !== null)) {
            return true;
        }
        try {
            return $user->can('mail.create') || $user->can('mail.update') || $user->hasRole('admin');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'request_id' => 'required|exists:requests,id',
            'response_id' => 'nullable|exists:request_responses,id',
            'response_type' => 'required|in:1,2,3', // 1=مقبول, 2=معلق, 3=مرفوض
            'notes' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // إذا كان الرد مرفوض (3)، يجب توضيح سبب الرفض
                    if ($this->input('response_type') == 3 && (empty($value) || $value == '-')) {
                        $fail('يجب توضيح سبب الرفض عند رفض الطلب');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'request_id.required' => 'رقم الطلب مطلوب',
            'request_id.exists' => 'الطلب المحدد غير موجود',
            'response_type.required' => 'نوع الرد مطلوب',
            'response_type.in' => 'نوع الرد غير صحيح',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ];
    }
}
