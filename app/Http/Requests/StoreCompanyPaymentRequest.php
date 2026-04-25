<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'        => ['required', 'date'],
            'montant'     => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'receipt'     => ['nullable', 'string'],
            'account'     => ['nullable', 'string'],
        ];
    }
}
