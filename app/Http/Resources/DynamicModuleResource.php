<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DynamicModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'icon'            => $this->icon,
            'show_in_sidebar' => $this->show_in_sidebar,
            'fields'          => $this->fields,
            'entries_count'   => $this->whenCounted('entries'),
            'created_at'      => $this->created_at,
        ];
    }
}
