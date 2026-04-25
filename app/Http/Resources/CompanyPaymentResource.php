<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'date'         => $this->date,
            'description'  => $this->description,
            'montant'      => $this->montant,
            'receipt'      => $this->receipt,
            'account'      => $this->account,
            'statut'       => $this->statut,
            'organization' => $this->whenLoaded('organization', fn () => $this->organization?->name),
            'created_at'   => $this->created_at,
        ];
    }
}
