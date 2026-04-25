<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name'      => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'account_holder' => ['required', 'string'],
            'type'           => ['required', 'in:BIF,USDT'],
        ];
    }
}
