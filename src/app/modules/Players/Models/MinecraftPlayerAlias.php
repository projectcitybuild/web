<?php

namespace App\Modules\Players\Models;

use App\Support\Model;

class MinecraftPlayerAlias extends Model
{
    protected $table = 'players_minecraft_aliases';

    protected $primaryKey = 'players_minecraft_alias_id';

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

    
    public function player()
    {
        return $this->hasOne('App\Modules\Players\Models\MinecraftPlayer', 'player_minecraft_id', 'player_minecraft_id');
    }
}
