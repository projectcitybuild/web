<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class DonationTier extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'donation_tiers';
    protected $auditInclude = [
        'name',
        'currency_reward',
    ];
    protected $fillable = [
        'name',
        'role_id',
    ];
    public $timestamps = false;

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            related: Role::class,
            foreignKey: 'role_id',
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
