<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AccountBalanceTransaction
{
    protected $table = 'account_balance_transactions';

    protected $primaryKey = 'balance_transaction_id';

    protected $fillable = [
        'account_id',
        'balance_before',
        'balance_after',
        'transaction_amount',
        'reason',
    ];

    protected $dates = [
        'created_at',
    ];

    public $timestamps = [
        'created_at',
    ];

    const UPDATED_AT = null;
}
