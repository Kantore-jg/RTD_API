<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email'],
            'phone'      => ['nullable', 'string'],
            'company'    => ['nullable', 'string'],
            'subject'    => ['required', 'string'],
            'message'    => ['required', 'string'],
        ];
    }
}
