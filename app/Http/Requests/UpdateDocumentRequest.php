<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $document = $this->route('document');
        return [
            'document_number' => 'required|string|unique:documents,document_number,' . $document->id,
            'document_type' => 'required|string',
            'document_date' => 'required|date',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'status' => 'required|in:draft,active,archived',
        ];
    }
}
