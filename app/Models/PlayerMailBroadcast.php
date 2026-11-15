<?php

namespace App\Models;

use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use Database\Factories\PlayerMailBroadcastFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMailBroadcast extends Model
{
    /** @use HasFactory<PlayerMailBroadcastFactory> */
    use HasFactory;

    protected $fillable = [
        'mail_id',
        'status',
        'max_player_id',
        'last_player_id',
        'recipients_count',
        'started_at',
        'completed_at',
    ];
    protected $casts = [
        'status' => PlayerMailBroadcastStatus::class,
        'max_player_id' => 'integer',
        'last_player_id' => 'integer',
        'recipients_count' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function mail(): BelongsTo
    {
        return $this->belongsTo(PlayerMail::class, 'mail_id');
    }
}
