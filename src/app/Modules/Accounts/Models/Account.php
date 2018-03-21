<?php

namespace App\Modules\Accounts\Models;

use App\Shared\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable {

    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'email',
        'password',
        'remember_token',
        'last_login_ip',
        'last_login_at',
    ];

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
