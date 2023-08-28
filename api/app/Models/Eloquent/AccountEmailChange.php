<?php

namespace App\Models\Eloquent;

use App\Model;

final class AccountEmailChange extends Model
{
    protected $table = 'account_email_changes';

    protected $primaryKey = 'account_email_change_id';

    protected $fillable = [
        'account_id',
        'token',
        'email_previous',
        'email_new',
        'is_confirmed',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
