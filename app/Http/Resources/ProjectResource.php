<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'category'    => $this->category,
            'status'      => $this->status,
            'progress'    => $this->progress,
            'budget'      => $this->budget,
            'deadline'    => $this->deadline,
            'team'        => $this->team,
            'tasks_count' => $this->whenCounted('tasks'),
            'created_at'  => $this->created_at,
        ];
    }
}
