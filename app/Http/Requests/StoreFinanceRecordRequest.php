<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'        => ['required', 'date'],
            'description' => ['required', 'string'],
            'type'        => ['required', 'in:Revenu,Dépense'],
            'montant'     => ['required', 'numeric', 'min:0'],
        ];
    }
}
