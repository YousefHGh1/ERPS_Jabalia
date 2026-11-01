<?php

namespace App\Http\Requests;

use App\Models\Request as RequestModel;
use App\Models\RequestWorkflow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update requests');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string|max:5000',
            'request_type_id' => 'required|exists:request_types,id',
            'mokalaf_id' => 'required|exists:mokalafs,id',
            'fee_amount' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'is_urgent' => 'boolean',
            'join' => 'boolean',
            'common_name' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $this->boolean('join')),
            ],
            'address' => 'nullable|string|max:500',
            'mobile' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'jebaia_no' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:100',
            'widget' => 'nullable|string|max:50',
            'voucher' => 'nullable|string|max:50',
            'divider' => 'nullable|string|max:50',
            'building' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'apartment' => 'nullable|string|max:50',
            'owner' => 'nullable|string|max:255',
            'tenant' => 'nullable|string|max:255',
            'approval_employee_ids' => 'nullable|string',
            'approval_employee_type' => 'nullable|in:primary,substitute',
            'approval_substitution_reason' => 'nullable|string|max:500',
            'workflow_employee_ids' => 'nullable|array',
            'workflow_employee_ids.*' => 'nullable|integer|exists:employees,id',
            'attachments.*' => [
                'nullable',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'text.required' => 'نص الطلب مطلوب',
            'text.max' => 'نص الطلب يجب ألا يتجاوز 5000 حرف',
            'request_type_id.required' => 'نوع الطلب مطلوب',
            'request_type_id.exists' => 'نوع الطلب المحدد غير موجود',
            'mokalaf_id.required' => 'المكلف مطلوب',
            'mokalaf_id.exists' => 'المكلف المحدد غير موجود',
            'common_name.required' => 'يرجى إدخال أسماء مقدمي الطلب عند تفعيل طلب مشترك',
            'fee_amount.numeric' => 'الرسم المستحق يجب أن يكون رقماً',
            'fee_amount.min' => 'الرسم المستحق لا يمكن أن يكون سالباً',
            'deadline.date' => 'تاريخ الاستحقاق غير صحيح',
            'deadline.after' => 'تاريخ الاستحقاق يجب أن يكون بعد اليوم',
            'attachments.*.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
            'attachments.*.mimes' => 'نوع الملف غير مسموح. الأنواع المسموحة: PDF, DOC, DOCX, JPG, JPEG, PNG, XLS, XLSX',
            'mobile.regex' => 'رقم الهاتف غير صحيح',
            'approval_employee_ids.exists' => 'أحد الموظفين المحددين غير موجود',
            'approval_employee_type.in' => 'نوع الموظف المسؤول غير صالح',
            'approval_substitution_reason.max' => 'سبب الاستبدال يجب ألا يتجاوز 500 حرف',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $fee = $this->input('fee_amount');
        $join = $this->has('join') ? (bool) $this->join : false;
        $this->merge([
            'is_urgent' => $this->has('is_urgent') ? (bool) $this->is_urgent : false,
            'join' => $join,
            'common_name' => $join ? $this->input('common_name') : null,
            'fee_amount' => ($fee === '' || $fee === null) ? null : $fee,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var RequestModel|null $requestModel */
            $requestModel = $this->route('request');
            if (! $requestModel instanceof RequestModel) {
                return;
            }
            $workflowEmployees = $this->input('workflow_employee_ids', []);
            $requestTypeId = $this->input('request_type_id');
            if (is_array($workflowEmployees)) {
                $workflows = RequestWorkflow::where('request_type_id', $requestTypeId)->active()->get()->keyBy('id');
                foreach ($workflowEmployees as $workflowId => $employeeId) {
                    if (!$employeeId) continue;
                    $workflow = $workflows->get($workflowId);
                    if (!$workflow) {
                        $validator->errors()->add("workflow_employee_ids.{$workflowId}", 'خطوة المسار لا تتبع نوع الطلب المحدد. حدّث الصفحة وأعد الاختيار.');
                    }
                }
            }
            $fee = $this->input('fee_amount');
            $hasActivePayments = $requestModel->payments()->where('status', 'active')->exists();
            if (($fee === null || $fee === '') && $hasActivePayments) {
                $validator->errors()->add(
                    'fee_amount',
                    'لا يمكن إزالة الرسم المستحق طالما توجد دفعات مسجّلة.'
                );

                return;
            }
            if ($fee === null || $fee === '') {
                return;
            }
            $paid = (float) $requestModel->payments()->where('status', 'active')->sum('amount');
            if ((float) $fee < $paid) {
                $validator->errors()->add(
                    'fee_amount',
                    'لا يمكن أن يكون الرسم المستحق أقل من مجموع الدفعات المسجّلة (' . number_format($paid, 2) . ' ₪).'
                );
            }

        });
    }
}
