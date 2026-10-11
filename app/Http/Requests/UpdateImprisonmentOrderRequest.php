<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImprisonmentOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $order = $this->route('imprisonment_order');
        return [
            'order_number' => 'required|string|unique:imprisonment_orders,order_number,' . $order->id,
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
            'status' => 'required|in:pending,active,completed,cancelled',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }
}
