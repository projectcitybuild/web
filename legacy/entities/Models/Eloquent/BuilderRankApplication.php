<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Library\Auditing\Contracts\LinkableAuditModel;

final class BuilderRankApplication extends Model implements LinkableAuditModel
{
    use HasFactory;
    use Notifiable;

    protected $table = 'builder_rank_applications';
    protected $primaryKey = 'id';
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

    public function routeNotificationForMail($notification)
    {
        return $this->account->email;
    }

    public function routeNotificationForDiscord(): string
    {
        return config('discord.webhook_architect_channel');
    }

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

    public function status(): ApplicationStatus
    {
        return ApplicationStatus::tryFrom($this->status);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.builder-ranks.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return "Builder Application {$this->getKey()}";
    }
}
