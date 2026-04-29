<?php

namespace App\Models;

use App\Traits\FormatsDatesSerialization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMessage extends Model
{
    use HasFactory, FormatsDatesSerialization;

    protected $fillable = [
        'organization_id',
        'user_id',
        'subject',
        'message',
        'read',
        'reply_text',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'read' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
