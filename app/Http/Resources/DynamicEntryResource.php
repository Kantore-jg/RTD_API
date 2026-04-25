<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DynamicEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'data'         => $this->data,
            'submitted_by' => $this->submitted_by,
            'created_at'   => $this->created_at,
        ];
    }
}
