<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'owner'       => ['required', 'string'],
            'email'       => ['required', 'email'],
            'phone'       => ['nullable', 'string'],
            'address'     => ['nullable', 'string'],
            'nif'         => ['nullable', 'string'],
            'monthly_fee' => ['nullable', 'numeric'],
        ];
    }
}
