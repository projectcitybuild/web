<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BuilderRankApplication extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'builder_rank_applications';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'minecraft_alias',
        'current_builder_rank',
        'build_location',
        'build_description',
        'additional_notes',
        'status',
        'denied_reason',
        'closed_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = [
        'closed_at',
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id',
        );
    }

    public function isReviewed(): bool
    {
        return $this->status == ApplicationStatus::DENIED->value
            || $this->status == ApplicationStatus::APPROVED->value;
    }
}
