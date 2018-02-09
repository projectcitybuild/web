<?php

namespace App\Modules\Players\Models;

use App\Shared\Model;

class MinecraftPlayerAlias extends Model {

    protected $table = 'players_minecraft_aliases';

    protected $primaryKey = 'player_minecraft_alias_id';

    protected $fillable = [
        'player_minecraft_id',
        'alias',
        'registered_at',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'registered_at',
        'created_at',
        'updated_at',
    ];

    
    public function player() {
        return $this->hasOne('App\Modules\Players\Models\MinecraftPlayer', 'player_minecraft_id', 'player_minecraft_id');
    }
}
