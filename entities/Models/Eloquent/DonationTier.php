<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Library\Auditing\AuditAttributes;
use Library\Auditing\Concerns\LogsActivity;
use Library\Auditing\Contracts\LinkableAuditModel;

/**
 * @property string name
 * @property int currency_reward
 */
final class DonationTier extends Model implements LinkableAuditModel
{
    use HasFactory;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_tiers';

    protected $primaryKey = 'donation_tier_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'currency_reward',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
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
