<?php

namespace App\Entities\Payments\Models;

use App\Entities\Accounts\Models\Account;
use App\Model;

/**
 * @deprecated 
 */
final class AccountPaymentSession extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_payment_sessions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'account_payment_session_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_processed' => 'boolean',
    ];

    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }
}
