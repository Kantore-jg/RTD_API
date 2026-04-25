<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileAccessLog extends Model
{
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
