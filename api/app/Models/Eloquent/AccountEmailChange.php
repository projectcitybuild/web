<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class AccountEmailChange extends Model
{
    protected $table = 'account_email_change';

    protected $primaryKey = 'account_email_change_id';

    protected $fillable = [
        'account_id',
        'token',
        'email_previous',
        'email_new',
        'expires_at',
        'is_confirmed',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(
            related: Account::class,
            foreignKey: 'account_id',
            localKey: 'account_id',
        );
    }
}
