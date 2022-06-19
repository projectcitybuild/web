<?php

namespace Entities\Models\Eloquent;

use App\Model;

/**
 * @deprecated
 */
final class GameWarning extends Model
{
    protected $table = 'game_network_warnings';
    protected $primaryKey = 'game_warning_id';
    protected $fillable = [
        'server_id',
        'warned_player_id',
        'staff_player_id',
        'reason',
        'weight',
        'is_active',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function warnedPlayer()
    {
        return $this->belongsTo(MinecraftPlayer::class, 'warned_player_id', 'player_minecraft_id');
    }

    public function staffPlayer()
    {
        return $this->belongsTo(MinecraftPlayer::class, 'staff_player_id', 'player_minecraft_id');
    }
}
