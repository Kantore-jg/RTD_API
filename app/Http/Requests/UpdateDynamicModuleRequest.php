<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDynamicModuleRequest extends FormRequest
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
            'fields'            => ['nullable', 'array'],
            'fields.*.id'       => ['required_with:fields', 'string'],
            'fields.*.label'    => ['required_with:fields', 'string'],
            'fields.*.type'     => ['required_with:fields', 'in:text,textarea,number,date,select,checkbox'],
            'fields.*.required' => ['nullable', 'boolean'],
            'fields.*.options'  => ['nullable', 'array'],
        ];
    }
}
