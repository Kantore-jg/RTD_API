<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDynamicModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'icon'              => ['nullable', 'string'],
            'show_in_sidebar'   => ['nullable', 'boolean'],
            'fields'            => ['required', 'array'],
            'fields.*.id'       => ['required', 'string'],
            'fields.*.label'    => ['required', 'string'],
            'fields.*.type'     => ['required', 'in:text,textarea,number,date,select,checkbox'],
            'fields.*.required' => ['nullable', 'boolean'],
            'fields.*.options'  => ['nullable', 'array'],
        ];
    }
}
