<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'project_id'  => ['nullable', 'exists:projects,id'],
            'status'      => ['nullable', 'in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED'],
            'priority'    => ['nullable', 'in:CRITICAL,HIGH,MEDIUM,LOW'],
            'progress'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'due_date'    => ['nullable', 'date'],
            'assignees'   => ['nullable', 'array'],
            'assignees.*' => ['exists:employees,id'],
        ];
    }
}
