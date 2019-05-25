<?php

namespace App\Entities\Servers\Models;

use App\Model;

/**
 * App\Entities\Servers\Models\ServerStatusPlayer
 *
 * @property int $server_status_player_id
 * @property int $server_status_id
 * @property string $player_type
 * @property int $player_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer wherePlayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer whereServerStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatusPlayer whereServerStatusPlayerId($value)
 * @mixin \Eloquent
 */
class ServerStatusPlayer extends Model
{
    protected $table = 'server_statuses_players';

    protected $primaryKey = 'server_status_player_id';

    protected $fillable = [
        'server_status_id',
        'player_type',
        'player_id',
    ];

    protected $hidden = [

    ];

    public $timestamps = false;


    public function player()
    {
        return $this->morphTo(null, 'player_type', 'player_id');
    }
}
