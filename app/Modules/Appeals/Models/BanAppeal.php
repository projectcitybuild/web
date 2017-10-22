<?php

namespace App\Modules\Bans\Models;

use Illuminate\Database\Eloquent\Model;

class BanAppeal extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ban_appeals';

    protected $primaryKey = 'ban_appeal_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_ban_id',
        'forum_user_id',
        'reason_unban',
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

    public function input() {
        return $this->hasMany('App\Modules\Appeals\BanAppealInput', 'ban_appeal_id', 'ban_appeal_id');
    }
}
