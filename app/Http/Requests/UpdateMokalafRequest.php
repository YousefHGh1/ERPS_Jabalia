<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMokalafRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mokalaf = $this->route('mokalaf');

        return [
            'name'     => 'required|string|max:255',
            'address'  => 'nullable|string|max:1000',
            'mobile'   => 'nullable|string|max:50',
            'identity_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('mokalafs', 'identity_number')->ignore($mokalaf),
            ],
            'license_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('mokalafs', 'license_number')->ignore($mokalaf),
            ],
        ];
    }
}
