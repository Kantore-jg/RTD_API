<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'plan'         => $this->plan,
            'monthly_fee'  => $this->monthly_fee,
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date,
            'status'       => $this->status,
            'organization' => $this->whenLoaded('organization', fn () => $this->organization?->name),
            'created_at'   => $this->created_at,
        ];
    }
}
