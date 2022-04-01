<?php

namespace App\Entities\Models\Eloquent;

use App\Model;

final class ServerStatusPlayer extends Model
{
    public $timestamps = false;
    protected $table = 'server_statuses_players';

    protected $primaryKey = 'server_status_player_id';

    protected $fillable = [
        'server_status_id',
        'player_type',
        'player_id',
    ];

    protected $hidden = [

    ];

    public function player()
    {
        return $this->morphTo(null, 'player_type', 'player_id');
    }
}
