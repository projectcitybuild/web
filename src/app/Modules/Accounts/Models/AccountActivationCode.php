<?php

namespace App\Modules\Accounts\Models;

use App\Shared\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AccountActivationCode extends Model {

    protected $table = 'account_activation_code';

    protected $primaryKey = 'account_activation_id';

    protected $fillable = [
        'token',
        'email',
        'password',
        'expires_at',
    ];
    
    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
    ];

}
