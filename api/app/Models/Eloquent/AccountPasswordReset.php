<?php

namespace App\Models\Eloquent;

use App\Model;

final class AccountPasswordReset extends Model
{
    protected $table = 'account_password_resets';

    protected $primaryKey = 'email';

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];

    public $incrementing = false;

    public $timestamps = false;
}
