<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DonationTier extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'donation_tiers';

    protected $primaryKey = 'donation_tier_id';

    protected $fillable = [
        'name',
        'group_id',
    ];

    public $timestamps = false;

    public function group(): BelongsTo
    {
        return $this->belongsTo(
            related: Group::class,
            foreignKey: 'group_id',
            ownerKey: 'group_id',
        );
    }

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
