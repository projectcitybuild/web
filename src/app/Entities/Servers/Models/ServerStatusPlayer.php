<?php

namespace App\Entities\Servers\Models;

use Application\Contracts\Model;

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
