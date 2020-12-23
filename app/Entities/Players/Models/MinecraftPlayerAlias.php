<?php

namespace App\Entities\Players\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class MinecraftPlayerAlias extends Model
{
    use HasFactory;

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
        return $this->hasOne('App\Entities\Players\Models\MinecraftPlayer', 'player_minecraft_id', 'player_minecraft_id');
    }
}
