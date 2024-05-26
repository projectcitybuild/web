<?php

namespace App\Models;

use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

final class BuilderRankApplication extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'builder_rank_application';

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
}
