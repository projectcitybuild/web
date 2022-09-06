<?php

namespace Entities\Models\Eloquent;

use App\Model;

final class GameWarning extends Model
{
    protected $table = 'game_network_warnings';
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
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warned_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function staffPlayer()
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'staff_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }
}
