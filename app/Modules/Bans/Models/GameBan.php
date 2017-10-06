<?php

namespace App\Modules\Bans\Models;

use Illuminate\Database\Eloquent\Model;

class GameBan extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_network_bans';

    protected $primaryKey = 'game_ban_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'server_id',
        'player_game_user_id',
        'staff_game_user_id',
        'banned_alias_id',
        'player_alias_at_ban',
        'reason',
        'is_active',
        'is_global_ban',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public function gameUser() {
        return $this->hasOne('App\Modules\Users\Models\GameUser', 'player_game_user_id', 'game_user_id');
    }
}
