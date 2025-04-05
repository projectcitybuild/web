<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

final class Donation extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'donations';

    protected $primaryKey = 'donation_id';

    protected $fillable = [
        'account_id',
        'amount',
        'payment_id',
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id'
        );
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            related: Payment::class,
            foreignKey: 'payment_id',
            ownerKey: 'payment_id',
        );
    }

    public function perks(): HasMany
    {
        return $this->hasMany(
            related: DonationPerk::class,
            foreignKey: 'donation_id',
            localKey: 'donation_id'
        );
    }

    public function formattedPaidAmount(): string
    {
        $payment = $this->payment;
        if ($payment !== null) {
            $money = new Money($payment->paid_unit_amount, new Currency($payment->paid_currency));
        } else {
            // Legacy
            $money = Money::USD($this->amount);
        }
        $numberFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());
        return $moneyFormatter->format($money);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.donations.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return "Donation {$this->getKey()}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('account_id', Account::class)
            ->add('amount', 'created_at');
    }
}
