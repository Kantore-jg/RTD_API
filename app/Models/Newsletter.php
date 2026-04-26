<?php

namespace App\Models;

use App\Traits\FormatsDatesSerialization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory, FormatsDatesSerialization;

    protected $fillable = [
        'subject',
        'content',
        'status',
        'recipients_count',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'recipients_count' => 'integer',
        ];
    }
}
