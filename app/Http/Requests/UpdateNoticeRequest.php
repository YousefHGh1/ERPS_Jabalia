<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $notice = $this->route('notice');
        return [
            'notice_number' => 'required|string|unique:notices,notice_number,' . $notice->id,
            'notice_date' => 'required|date',
            'notice_reason_id' => 'required|exists:notice_reasons,id',
            'rent_location_id' => 'nullable|exists:rent_locations,id',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,sent,delivered,cancelled',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }
}
