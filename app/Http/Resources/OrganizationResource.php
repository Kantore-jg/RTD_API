<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'domain'        => $this->domain,
            'address'       => $this->address,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'company_email' => $this->company_email,
            'logo'          => $this->logo,
            'nif'           => $this->nif,
            'plan'          => $this->plan,
            'monthly_fee'   => $this->monthly_fee,
            'modules'       => $this->modules,
            'status'        => $this->status,
            'users_count'   => $this->whenCounted('users'),
            'created_at'    => $this->created_at,
        ];
    }
}
