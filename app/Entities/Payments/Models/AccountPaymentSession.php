<?php

namespace App\Entities\Payments\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;

final class AccountPaymentSession extends Model
{
    /**
     * The table associated with the model.
     */
    protected string $table = 'account_payment_sessions';

    /**
     * The primary key associated with the table.
     */
    protected string $primaryKey = 'account_payment_session_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'session_id',
        'account_id',
        'is_processed',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected array $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected array $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'is_processed' => 'boolean',
    ];

    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
