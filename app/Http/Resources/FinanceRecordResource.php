<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'date'        => $this->date,
            'description' => $this->description,
            'type'        => $this->type,
            'montant'     => $this->montant,
            'statut'      => $this->statut,
            'created_at'  => $this->created_at,
        ];
    }
}
