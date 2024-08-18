<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
    use HasStaticTable;

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
}
