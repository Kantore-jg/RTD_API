<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchivedFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'original_name'     => $this->original_name,
            'type'              => $this->type,
            'category'          => $this->category,
            'size'              => $this->formattedSize(),
            'uploader'          => $this->whenLoaded('uploader', fn () => $this->uploader?->name),
            'folder_id'         => $this->folder_id,
            'access_logs_count' => $this->whenCounted('accessLogs'),
            'created_at'        => $this->created_at,
        ];
    }

    protected function formattedSize(): string
    {
        $bytes = $this->size ?? 0;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
