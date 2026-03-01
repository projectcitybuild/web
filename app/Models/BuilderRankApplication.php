<?php

namespace App\Models;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

final class BuilderRankApplication extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use Notifiable;

    protected $table = 'builder_rank_applications';
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
        'next_reminder_at',
        'created_at',
        'updated_at',
    ];
    public $timestamps = [
        'closed_at',
        'next_reminder_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'status' => ApplicationStatus::class,
    ];

    public function routeNotificationForMail($notification)
    {
        return $this->account->email;
    }

    public function routeNotificationForDiscord(): string
    {
        return config('discord.webhook_architect_forum_channel');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
        );
    }

    public function isReviewed(): bool
    {
        return $this->status == ApplicationStatus::DENIED
            || $this->status == ApplicationStatus::APPROVED;
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::PENDING);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('review.builder-ranks.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return "Builder Application {$this->id}";
    }
}
