<?php

namespace App\Modules\Servers\Models;

use App\Shared\Model;

class ServerStatus extends Model {

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


    public function players() {
        return $this->hasMany('App\Modules\Servers\Models\ServerStatusPlayer', 'server_status_id', 'server_status_id');
    }
}
