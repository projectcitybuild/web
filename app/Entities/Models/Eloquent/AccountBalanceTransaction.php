<?php

namespace App\Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AccountBalanceTransaction extends Model
{
    protected $table = 'account_balance_transactions';

    protected $primaryKey = 'balance_transaction_id';

    protected $fillable = [
        'account_id',
        'balance_before',
        'balance_after',
        'transaction_amount',
        'transaction_type',
        'reason',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id',
        );
    }
}
