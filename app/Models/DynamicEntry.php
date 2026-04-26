<?php

namespace App\Models;

use App\Traits\FormatsDatesSerialization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicEntry extends Model
{
    use HasFactory, FormatsDatesSerialization;

    protected $fillable = [
        'dynamic_module_id',
        'data',
        'submitted_by',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(DynamicModule::class, 'dynamic_module_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
