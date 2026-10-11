<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update vehicle');
    }

    public function rules(): array
    {
        return [
            'number'   => 'required|string|max:255',
            'name'     => 'required|string|max:255',
            'color'    => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required'   => __('Required'),
            'name.required'     => __('Required'),
            'color.required'    => __('Required'),
            'images.*.image'    => __('Invalid image file'),
            'images.*.mimes'    => __('Supported image formats: jpeg, png, jpg, gif'),
            'images.*.max'      => __('Maximum file size allowed: 2MB'),
        ];
    }
}
