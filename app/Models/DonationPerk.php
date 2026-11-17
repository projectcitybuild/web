<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\AppendsSystemAuditTag;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use function now;

final class DonationPerk extends Model implements AuditableContract, LinkableAuditModel
{
    use AppendsSystemAuditTag;
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'donation_perks';
    protected $auditInclude = [
        'donation_id',
        'account_id',
        'donation_tier_id',
        'is_active',
        'expires_at',
    ];
    protected $fillable = [
        'donation_id',
        'account_id',
        'donation_tier_id',
        'is_active',
        'expires_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'last_currency_reward_at' => 'datetime',
        'is_active' => 'boolean',
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
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id');
    }

    public function donationTier(): BelongsTo
    {
        return $this->belongsTo(DonationTier::class, 'donation_tier_id');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.donations.show', $this->donation_id)
            .'#perk-'.$this->id;
    }

    public function getActivitySubjectName(): ?string
    {
        return "Donation Perk {$this->id}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('donation_id', Donation::class)
            ->addRelationship('donation_tier_id', DonationTier::class)
            ->addRelationship('account_id', Account::class)
            ->addBoolean('is_active')
            ->add('expires_at');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function transformAudit(array $data): array
    {
        return $this->appendSystemAuditTag($data);
    }
}
