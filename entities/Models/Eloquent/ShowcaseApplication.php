<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Library\Auditing\Contracts\LinkableAuditModel;

final class ShowcaseApplication extends Model implements LinkableAuditModel
{
    use Notifiable;

    protected $table = 'showcase_applications';
    protected $fillable = [
        'account_id',
        'name',
        'title',
        'description',
        'creators',
        'location_world',
        'location_x',
        'location_y',
        'location_z',
        'location_pitch',
        'location_yaw',
        'built_at',
        'status',
        'denied_reason',
        'closed_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'status' => ApplicationStatus::class,
    ];
    public $timestamps = [
        'built_at',
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

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.showcase-apps.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return "Showcase Application {$this->getKey()}";
    }
}
