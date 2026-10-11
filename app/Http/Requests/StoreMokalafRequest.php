<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMokalafRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('license_number') && $this->license_number === '') {
            $this->merge(['license_number' => null]);
        }
        if ($this->has('identity_number') && $this->identity_number === '') {
            $this->merge(['identity_number' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'identity_number' => [
                'required_without:license_number',
                'nullable',
                'string',
                'max:50',
                Rule::unique('mokalafs', 'identity_number'),
            ],
            'license_number' => [
                'required_without:identity_number',
                'nullable',
                'string',
                'max:50',
                Rule::unique('mokalafs', 'license_number'),
            ],
            'name' => 'required|string',
            'address' => 'nullable|string',
            'mobile' => 'nullable|string',
        ];
    }
}
