<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrgSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['nullable', 'string'],
            'domain'        => ['nullable', 'string'],
            'address'       => ['nullable', 'string'],
            'phone'         => ['nullable', 'string'],
            'email'         => ['nullable', 'email'],
            'company_email' => ['nullable', 'email'],
        ];
    }
}
