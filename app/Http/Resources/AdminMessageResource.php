<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'subject'      => $this->subject,
            'message'      => $this->message,
            'is_read'      => $this->is_read,
            'user'         => $this->whenLoaded('user', fn () => $this->user?->name),
            'organization' => $this->whenLoaded('organization', fn () => $this->organization?->name),
            'created_at'   => $this->created_at,
        ];
    }
}
