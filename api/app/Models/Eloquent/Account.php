<?php

namespace App\Models\Eloquent;

use Illuminate\Foundation\Auth\User as Authenticatable;

final class Account extends Authenticatable
{
    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'email',
        'username',
        'password',
        'remember_token',
        'last_login_ip',
        'last_login_at',
        'balance',
    ];

    protected $hidden = [
        'totp_secret',
        'totp_backup_code',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    protected $casts = [
        'is_totp_enabled' => 'boolean',
    ];
}
