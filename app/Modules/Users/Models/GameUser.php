<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;

class GameUser extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_users';

    protected $primaryKey = 'game_user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
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

    
    public function user() {
        return $this->hasMany('App\Modules\Users\Models\User', 'user_id', 'user_id');
    }

    public function aliases() {
        return $this->belongsTo('App\Modules\Users\Models\UserAlias', 'game_user_id', 'game_user_id');
    }
}
