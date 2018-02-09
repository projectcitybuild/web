<?php

namespace App\Modules\Players\Models;

use App\Shared\Model;

class MinecraftPlayer extends Model {

    protected $table = 'players_minecraft';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'account_id',
        'playtime',
        'last_seen_at',
    ];

    protected $hidden = [
        
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_seen_at',
    ];

    
    public function account() {
        return $this->hasMany('App\Modules\Accounts\Models\Account', 'account_id', 'account_id');
    }

    public function aliases() {
        return $this->hasMany('App\Modules\Players\Models\MinecraftPlayerAlias', 'player_minecraft_id', 'player_minecraft_id');
    }
}
