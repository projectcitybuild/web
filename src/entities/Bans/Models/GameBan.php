<?php

namespace Entities\Bans\Models;

use Application\Contracts\Model;

class GameBan extends Model
{
    protected $table = 'game_network_bans';

    protected $primaryKey = 'game_ban_id';

    protected $fillable = [
        'server_id',
        'banned_player_id',
        'banned_player_type',
        'banned_alias_at_time',
        'staff_player_id',
        'staff_player_type',
        'reason',
        'is_active',
        'is_global_ban',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function bannedPlayer()
    {
        return $this->morphTo(null, 'banned_player_type', 'banned_player_id');
    }

    public function staffPlayer()
    {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }

    public function unban()
    {
        return $this->belongsTo('Entities\Bans\Models\GameUnban', 'game_ban_id', 'game_ban_id');
    }
}
