<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerReportAttachment extends Model
{
    protected $fillable = [
        'path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function playerReport(): BelongsTo
    {
        return $this->belongsTo(PlayerReport::class);
    }
}
