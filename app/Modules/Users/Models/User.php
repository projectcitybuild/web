<?php

namespace App\Modules\Users\Models;

use App\Shared\Model;

class User extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

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


    public function gameUser() {
        return $this->belongsTo('App\Modules\Users\Models\GameUser', 'user_id', 'user_id');
    }
}
