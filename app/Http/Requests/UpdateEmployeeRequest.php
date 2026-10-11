<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('employee');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'emp_number' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('employees')->ignore($employeeId)],
            'national_id' => ['sometimes', 'required', 'digits:10', Rule::unique('employees')->ignore($employeeId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees')->ignore($employeeId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['sometimes', 'required', Rule::in(['male', 'female'])],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'Active', 'InActive'])],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widow'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'place_of_work' => ['nullable', 'string', 'max:255'],
            'job_id' => ['nullable', 'exists:jobs,id'],
            'hire_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $status = $this->input('status');
            if (is_string($status)) {
                $this->merge(['status' => strtolower($status)]);
            }
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => __('الاسم مطلوب'),
            'emp_number.required' => __('الرقم الوظيفي مطلوب'),
            'emp_number.unique' => __('الرقم الوظيفي موجود مسبقاً'),
            'national_id.required' => __('رقم الهوية مطلوب'),
            'national_id.digits' => __('رقم الهوية يجب أن يتكون من 10 أرقام'),
            'email.email' => __('البريد الإلكتروني غير صالح'),
            'email.unique' => __('البريد الإلكتروني موجود مسبقاً'),
            'password.min' => __('كلمة المرور يجب أن تكون 8 أحرف على الأقل'),
            'password.confirmed' => __('تأكيد كلمة المرور غير متطابق'),
        ];
    }
}
