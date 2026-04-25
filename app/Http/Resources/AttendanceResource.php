<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'employee' => $this->whenLoaded('employee', fn () => [
                'id'   => $this->employee->id,
                'name' => $this->employee->name,
            ]),
            'date'     => $this->date,
            'arrivee'  => $this->arrivee,
            'depart'   => $this->depart,
            'statut'   => $this->statut,
            'poste'    => $this->poste,
        ];
    }
}
