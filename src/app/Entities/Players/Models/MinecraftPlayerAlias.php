<?php

namespace App\Entities\Players\Models;

use App\Model;

/**
 * App\Entities\Players\Models\MinecraftPlayerAlias
 *
 * @property int $players_minecraft_alias_id
 * @property int $player_minecraft_id
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $registered_at The actual datetime they changed their alias to this
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Players\Models\MinecraftPlayer $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias wherePlayerMinecraftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias wherePlayersMinecraftAliasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias whereRegisteredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayerAlias whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        return $this->hasOne('App\Entities\Players\Models\MinecraftPlayer', 'player_minecraft_id', 'player_minecraft_id');
    }
}
