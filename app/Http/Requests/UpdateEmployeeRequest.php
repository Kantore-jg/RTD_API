<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email'],
            'phone'       => ['nullable', 'string'],
            'role'        => ['nullable', 'string'],
            'department'  => ['nullable', 'string'],
            'joined_at'   => ['nullable', 'date'],
            'identifiant' => ['nullable', 'email'],
            'password'    => ['nullable', 'string', 'min:6'],
        ];
    }
}
