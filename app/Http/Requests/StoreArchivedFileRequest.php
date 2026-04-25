<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArchivedFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'      => ['required', 'file', 'max:51200'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'category'  => ['nullable', 'string'],
        ];
    }
}
