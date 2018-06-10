<?php

namespace App\Modules\Bans\Models;

use App\core\Model;

class GameUnban extends Model {

    protected $table = 'game_network_unbans';

    protected $primaryKey = 'game_unban_id';

    protected $fillable = [
        'game_ban_id',
        'staff_player_id',
        'staff_player_type',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function ban() {
        return $this->hasOne('App\Modules\Bans\Models\GameBan', 'game_ban_id', 'game_ban_id');
    }

    public function staffPlayer() {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }

}
