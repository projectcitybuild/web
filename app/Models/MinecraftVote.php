<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinecraftVote extends Model
{
    use HasStaticTable;

    protected $table = 'minecraft_votes';
    protected $fillable = [
        'server_id',
        'player_id',
        'event_id',
        'service_name',
        'address',
        'source_timestamp',
        'voted_at',
        'additional_data',
    ];
    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(MinecraftPlayer::class);
    }
}
