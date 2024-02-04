<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

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
