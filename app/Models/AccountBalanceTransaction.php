<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AccountBalanceTransaction extends Model implements LinkableAuditModel
{
    use LogsActivity;

    protected $table = 'account_balance_transactions';

    protected $primaryKey = 'balance_transaction_id';

    protected $fillable = [
        'account_id',
        'balance_before',
        'balance_after',
        'transaction_amount',
        'reason',
    ];

    public $timestamps = [
        'created_at',
    ];

    const UPDATED_AT = null;

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return null;
    }

    public function getActivitySubjectName(): ?string
    {
        return "Transaction {$this->getKey()}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('account_id', Account::class)
            ->add('balance_before', 'balance_after', 'transaction_amount', 'reason');
    }
}
