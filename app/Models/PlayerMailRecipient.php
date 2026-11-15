<?php

namespace App\Models;

use Database\Factories\PlayerMailRecipientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerMailRecipient extends Model
{
    /** @use HasFactory<PlayerMailRecipientFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'mail_id',
        'player_id',
        'read_at',
        'received_at',
    ];
    protected $casts = [
        'read_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function mail(): BelongsTo
    {
        return $this->belongsTo(PlayerMail::class, 'mail_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(MinecraftPlayer::class, 'player_id');
    }
}
