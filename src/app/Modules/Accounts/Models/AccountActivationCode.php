<?php

namespace App\Modules\Accounts\Models;

use App\Shared\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AccountActivationCode extends Model {

    protected $table = 'account_activation_codes';

    protected $primaryKey = 'account_activation_id';

    protected $fillable = [
        'token',
        'email',
        'password',
        'is_used',
        'expires_at',
    ];
    
    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
    ];

    public function getActivationUrl() : string {
        return route('front.register.activate', [
            'token' => $this->token,
        ]);
    }

}
