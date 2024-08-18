<?php

namespace App\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

final class DonationTier extends Model implements LinkableAuditModel
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'donation_tiers';

    protected $primaryKey = 'donation_tier_id';

    protected $fillable = [
        'name',
        'currency_reward',
    ];
    public $timestamps = false;

    public function getActivitySubjectLink(): ?string
    {
        return null;
    }

    public function getActivitySubjectName(): ?string
    {
        return "Donation Tier {$this->name}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('name', 'currency_reward');
    }
}
