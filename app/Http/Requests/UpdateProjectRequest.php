<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string'],
            'status'      => ['nullable', 'in:En cours,Planifié,Urgent,Terminé'],
            'budget'      => ['nullable', 'string'],
            'deadline'    => ['nullable', 'date'],
            'team'        => ['nullable', 'array'],
        ];
    }
}
