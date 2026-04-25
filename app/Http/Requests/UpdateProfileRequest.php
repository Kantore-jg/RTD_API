<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string'],
            'address'    => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'position'   => ['nullable', 'string'],
            'bio'        => ['nullable', 'string'],
        ];
    }
}
