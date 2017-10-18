<?php

namespace App\Modules\Servers\Models;

use Illuminate\Database\Eloquent\Model;

class ServerStatus extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_statuses';

    protected $primaryKey = 'server_status_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'server_id',
        'is_online',
        'num_of_players',
        'num_of_slots',
        'players',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

}
