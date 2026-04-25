<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'avatar'          => $this->avatar,
            'role'            => $this->role,
            'organization_id' => $this->organization_id,
            'employee_id'     => $this->whenLoaded('employee', fn () => $this->employee?->id),
            'organization'    => new OrganizationResource($this->whenLoaded('organization')),
            'created_at'      => $this->created_at,
        ];
    }
}
