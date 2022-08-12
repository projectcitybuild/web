<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Carbon\CarbonInterface;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class BanAppeal extends Model
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

    public function gameBan(): BelongsTo
    {
        return $this->belongsTo(GameBan::class, 'game_ban_id', 'game_ban_id');
    }

    public function deciderAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'decider_account_id', 'account_id');
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
        return $this->gameBan->bannedPlayer->account?->email ?? $this->email;
    }

    public function routeNotificationForDiscord(): string
    {
        return config('discord.webhook_ban_appeal_channel');
    }

    public function getBannedPlayerName()
    {
        return $this->gameBan->bannedPlayer->getBanReadableName() ??
            $this->gameBan->banned_alias_at_time;
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

        return $this->gameBan->expires_at->diffForHumans($this->decided_at, CarbonInterface::DIFF_ABSOLUTE);
    }
}
