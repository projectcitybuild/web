<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerWarning extends Model
{
    protected $table = 'player_warnings';
    protected $fillable = [
        'warned_player_id',
        'warner_player_id',
        'reason',
        'weight',
        'created_at',
        'updated_at',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function warnedPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warned_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function warnerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }
}
