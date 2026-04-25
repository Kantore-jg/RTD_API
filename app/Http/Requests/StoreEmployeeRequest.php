<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email'],
            'phone'       => ['nullable', 'string'],
            'role'        => ['required', 'string'],
            'department'  => ['nullable', 'string'],
            'joined_at'   => ['nullable', 'date'],
            'identifiant' => ['nullable', 'email'],
            'password'    => ['nullable', 'string', 'min:6'],
        ];
    }
}
