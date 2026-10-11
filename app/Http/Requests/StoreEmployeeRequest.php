<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // أو استخدم Policy
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            // emp_number is optional at creation; generated later from profile if absent
            'emp_number'     => ['nullable', 'string', 'max:50', 'unique:employees,emp_number'],
            // the create modal uses `czn_id` field name
            'czn_id'         => ['required', 'digits:9', 'unique:employees,czn_id'],
            'email'          => ['nullable', 'email', 'max:255', 'unique:employees,email'],
            // password is optional on creation; will be set later (e.g. to emp_number)
            'password'       => ['nullable', 'string', 'min:8', 'confirmed'],
            'mobile'         => ['required', 'string', 'max:20'],
            'gender'         => ['required', Rule::in(['male', 'female'])],
            'birthdate'      => ['nullable', 'date', 'before:today'],
            'birthplace'     => ['nullable', 'string', 'max:255'],
            // accept both UI capitalized values and normalized lowercase values
            'status'         => ['nullable', Rule::in(['active', 'inactive', 'Active', 'InActive'])],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widow'])],
            'department_id'  => ['nullable', 'exists:departments,id'],
            'section_id'     => ['nullable', 'exists:sections,id'],
            'branch_id'      => ['nullable', 'exists:branches,id'],
            'place_of_work'  => ['nullable', 'string', 'max:255'],
            'job_id'         => ['nullable', 'exists:jobs,id'],
            'hire_date'      => ['nullable', 'date'],
            'salary'         => ['nullable', 'numeric', 'min:0'],
            'address'        => ['nullable', 'string', 'max:500'],
            'photo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
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
            'name.required'        => __('الاسم مطلوب'),
            'emp_number.required'  => __('الرقم الوظيفي مطلوب'),
            'emp_number.unique'    => __('الرقم الوظيفي موجود مسبقاً'),
            'czn_id.required'      => __('رقم الهوية مطلوب'),
            'czn_id.digits'        => __('رقم الهوية يجب أن يتكون من 9 أرقام'),
            'mobile.required'      => __('رقم الهاتف مطلوب'),
            'mobile.max'           => __('رقم الهاتف طويل جداً'),
            'email.email'          => __('البريد الإلكتروني غير صالح'),
            'email.unique'         => __('البريد الإلكتروني موجود مسبقاً'),
            'password.required'    => __('كلمة المرور مطلوبة'),
            'password.min'         => __('كلمة المرور يجب أن تكون 8 أحرف على الأقل'),
            'password.confirmed'   => __('تأكيد كلمة المرور غير متطابق'),
            'gender.required'      => __('الجنس مطلوب'),
            'department_id.exists' => __('القسم المحدد غير موجود'),
            'job_id.exists'        => __('المسمى الوظيفي غير موجود'),
        ];
    }

    public function attributes(): array
    {
        return [
            'emp_number'    => __('الرقم الوظيفي'),
            'czn_id'        => __('رقم الهوية'),
            'mobile'        => __('رقم الهاتف'),
            'department_id' => __('القسم'),
            'section_id'    => __('الشعبة'),
            'branch_id'     => __('الفرع'),
            'place_of_work' => __('place_of_work'),
            'job_id'        => __('المسمى الوظيفي'),
        ];
    }
}
