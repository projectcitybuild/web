<?php

namespace Domains\Modules\Payments\Models;

use Domains\Model;
use Domains\Modules\Accounts\Models\Account;

class AccountPayment extends Model
{
    protected $table = 'account_payments';
    protected $primaryKey = 'donation_id';

    protected $fillable = [
        'payment_type',
        'payment_id',
        'payment_amount',
        'payment_source',
        'account_id',
        'is_processed',
        'is_refunded',
        'is_subscription_payment',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

    public function associated()
    {
        return $this->morphTo('payment_id', 'payment_type');
    }
}
