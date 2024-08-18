<?php

namespace App\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;
use function now;

final class DonationPerk extends Model implements LinkableAuditModel
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'donation_perks';

    protected $primaryKey = 'donation_perks_id';

    protected $fillable = [
        'donation_id',
        'account_id',
        'donation_tier_id',
        'is_active',
        'expires_at',
        'created_at',
        'updated_at',
        'last_currency_reward_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_currency_reward_at' => 'datetime',
        'is_active' => 'boolean',
        'is_lifetime_perks' => 'boolean',
    ];

    public function isActive(): bool
    {
        if ($this->expires_at !== null && now()->gte($this->expires_at)) {
            return false;
        }

        return $this->is_active;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id', 'donation_id');
    }

    public function donationTier(): BelongsTo
    {
        return $this->belongsTo(DonationTier::class, 'donation_tier_id', 'donation_tier_id');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.donations.show', $this->donation_id)
            .'#perk-'.$this->getKey();
    }

    public function getActivitySubjectName(): ?string
    {
        return "Donation Perk {$this->getKey()}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('donation_id', Donation::class)
            ->addRelationship('donation_tier_id', DonationTier::class)
            ->addRelationship('account_id', Account::class)
            ->addBoolean('is_active', 'is_lifetime_perks')
            ->add('expires_at');
    }
}
