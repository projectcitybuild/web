<?php

namespace App\Modules\Bans\Models;

use Illuminate\Database\Eloquent\Model;

class BanAppealInput extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ban_appeal_input';

    protected $primaryKey = 'ban_appeal_input_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ban_appeal_id',
        'server_id',
        'banned_by',
        'date_of_ban',
        'reason_ban',
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

    public function appeal() {
        return $this->hasMany('App\Modules\Appeals\BanAppeal', 'ban_appeal_id', 'ban_appeal_id');
    }
}
