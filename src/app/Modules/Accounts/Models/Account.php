<?php

namespace App\Modules\Accounts\Models;

use App\Shared\Model;

class Account extends Model {

    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'email',
        'password',
        'remember_token',
        'last_login_ip',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    public function minecraftAccount() {
        return $this->belongsTo('App\Modules\Players\Models\MinecraftPlayer', 'account_id', 'account_id');
    }

}
