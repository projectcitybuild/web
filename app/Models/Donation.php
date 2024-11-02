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
