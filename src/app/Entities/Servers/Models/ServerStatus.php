<?php

namespace App\Entities\Servers\Models;

use App\Model;

/**
 * App\Entities\Servers\Models\ServerStatus
 *
 * @property int $server_status_id
 * @property int $server_id
 * @property mixed $is_online
 * @property int $num_of_players Number of players currently connected
 * @property int $num_of_slots Maximum number of players the server can hold
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Servers\Models\ServerStatusPlayer[] $players
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereNumOfPlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereNumOfSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereServerStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServerStatus extends Model
{
    protected $table = 'server_statuses';

    protected $primaryKey = 'server_status_id';

    protected $fillable = [
        'server_id',
        'is_online',
        'num_of_players',
        'num_of_slots',
        'created_at',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];


    public function players()
    {
        return $this->hasMany('App\Entities\Servers\Models\ServerStatusPlayer', 'server_status_id', 'server_status_id');
    }
}
