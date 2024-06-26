<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
    protected $table = 'payment';

    protected $fillable = [
        'account_id',
        'stripe_price',
        'stripe_product',
        'amount_paid_in_cents',
        'quantity',
        'is_subscription_payment',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
