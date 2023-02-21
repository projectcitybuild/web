<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

/**
 * @property int account_id
 * @property int balance_before
 * @property int balance_after
 * @property int transaction_amount
 * @property string reason
 * @property Carbon created_at
 * @property ?Account account
 */
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
    protected $dates = [
        'created_at',
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
