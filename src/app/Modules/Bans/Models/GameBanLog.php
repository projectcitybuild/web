<?php

namespace App\Modules\Bans\Models;

use App\Shared\Model;

class GameBanLog extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_network_ban_logs';

    protected $primaryKey = 'game_ban_log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_ban_id',
        'server_key_id',
        'ban_action',
        'incoming_ip',
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
    ];

    public $timestamps = false;

}
