<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'bank_name'       => $this->bank_name,
            'account_number'  => $this->account_number,
            'account_holder'  => $this->account_holder,
            'type'            => $this->type,
            'created_at'      => $this->created_at,
        ];
    }
}
