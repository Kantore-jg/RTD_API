<?php

namespace App\Models;

use App\Traits\FormatsDatesSerialization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileAccessLog extends Model
{
    use FormatsDatesSerialization;
    protected $fillable = [
        'archived_file_id',
        'user_id',
        'action',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(ArchivedFile::class, 'archived_file_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
