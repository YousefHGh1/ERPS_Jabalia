<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_number' => 'required|string|unique:documents,document_number',
            'document_type' => 'required|string',
            'document_date' => 'required|date',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240',
        ];
    }
}
