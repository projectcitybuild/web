<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

final class Donation extends Model implements LinkableAuditModel
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'donations';
    protected $primaryKey = 'donation_id';
    protected $fillable = [
        'account_id',
        'amount',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function perks(): HasMany
    {
        return $this->hasMany(DonationPerk::class, 'donation_id', 'donation_id');
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.donations.show', $this);
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
