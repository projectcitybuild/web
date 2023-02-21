<?php

namespace App\Models\Eloquent;

use App\Model;
use Carbon\CarbonInterface;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Library\Auditing\Contracts\LinkableAuditModel;

class BanAppeal extends Model implements LinkableAuditModel
{
    use HasFactory, Notifiable;

    protected $casts = [
        'status' => BanAppealStatus::class,
        'decided_at' => 'datetime',
    ];
    protected $fillable = [
        'game_ban_id',
        'is_account_verified',
        'explanation',
        'email',
        'status',
    ];

    public function gamePlayerBan(): BelongsTo
    {
        return $this->belongsTo(
            related: GamePlayerBan::class,
            foreignKey: 'game_ban_id',
            ownerKey: 'id',
        );
    }

    public function deciderPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'decider_player_minecraft_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', BanAppealStatus::PENDING);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->whereNot('status', BanAppealStatus::PENDING);
    }

    public function isPending()
    {
        return $this->status == BanAppealStatus::PENDING;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->gamePlayerBan->bannedPlayer->account?->email ?? $this->email;
    }

    public function routeNotificationForDiscord(): string
    {
        return config('discord.webhook_ban_appeal_channel');
    }

    public function getBannedPlayerName()
    {
        return $this->gamePlayerBan->bannedPlayer->getBanReadableName() ??
            $this->gamePlayerBan->banned_alias_at_time;
    }

    public function showLink(): string
    {
        return $this->is_account_verified ?
            route('front.appeal.show', $this) :
            URL::signedRoute('front.appeal.show', ['banAppeal' => $this]);
    }

    public function getDecisionTempbanDuration()
    {
        if ($this->status != BanAppealStatus::ACCEPTED_TEMPBAN) {
            return null;
        }

        return $this->gamePlayerBan->expires_at->diffForHumans($this->decided_at, CarbonInterface::DIFF_ABSOLUTE);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.ban-appeals.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return "Ban Appeal {$this->getKey()}";
    }
}
