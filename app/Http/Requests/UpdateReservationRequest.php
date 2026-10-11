<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('subject') && $this->has('reservation_no')) {
            return [
                'reservation_no' => 'required|integer',
                'subject' => 'required|string|max:65535',
                'voucher' => 'required|string|max:255',
                'widget' => 'required|string|max:255',
                'area' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'jebaia_no' => 'nullable|numeric',
                'license_file' => 'nullable|string|max:255',
                'contents' => 'nullable|string',
                'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            ];
        }

        $reservation = $this->route('reservation');
        return [
            'reservation_number' => 'required|string|unique:reservations,reservation_number,' . $reservation->id,
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'court_name' => 'required|string',
            'judge_name' => 'required|string',
            'case_number' => 'required|string',
            'case_year' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function messages(): array
    {
        if ($this->has('subject') && $this->has('reservation_no')) {
            return [
                'reservation_no.required' => 'يرجى إدخال رقم الحجز',
                'subject.required' => 'يرجى إدخال موضوع الحجز',
                'voucher.required' => 'يرجى إدخال القسيمة',
                'widget.required' => 'يرجى إدخال القطعة',
            ];
        }
        return [];
    }
}
