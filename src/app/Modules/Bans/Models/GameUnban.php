<?php

namespace App\Modules\Bans\Models;

use App\Shared\Model;

class GameUnban extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_network_unbans';

    protected $primaryKey = 'game_ban_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_ban_id',
        'staff_game_user_id',
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
