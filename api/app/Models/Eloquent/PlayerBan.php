<?php

namespace App\Models\Eloquent;

use App\Models\UnbanType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerBan extends Model
{
    use HasFactory;

    protected $table = 'player_bans';

    protected $fillable = [
        'banned_player_id',
        'banned_alias_at_time',
        'banner_player_id',
        'reason',
        'expires_at',
        'created_at',
        'updated_at',
        'unbanned_at',
        'unbanner_player_id',
        'unban_type',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
        'unbanned_at',
    ];

    public function scopeForPlayer(Builder $query, Player $player)
    {
        $query->where('banned_player_id', $player->getKey());
    }

    public function scopeActive(Builder $query)
    {
        $query->whereNull('unbanned_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', now());
            });
    }

    public function unbannedAt(): Attribute
    {
        // To simplify checking whether a ban is active, we'll always use the `unbanned_at`
        // property as the source of truth. However, there are cases where the ban has expired
        // but doesn't have an `unbanned_at` value yet (updated later by a scheduled task).
        // In those cases, we'll return the `expired_at` value instead so that the ban is treated
        // as inactive.
        return Attribute::make(
            get: function ($unbannedAt) {
                if ($unbannedAt !== null) {
                    return new Carbon($unbannedAt);
                }
                if ($this->expires_at !== null && $this->expires_at->lte(now())) {
                    return $this->expires_at;
                }

                return null;
            },
        );
    }

    public function unbanType(): Attribute
    {
        return Attribute::make(
            get: function ($unbanType) {
                if ($unbanType !== null) {
                    return UnbanType::tryFrom($unbanType);
                }
                if ($this->expires_at !== null && $this->expires_at->lte(now())) {
                    return UnbanType::EXPIRED;
                }

                return null;
            },
        );
    }

    public function bannedPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'banned_player_id',
            ownerKey: Player::primaryKey(),
        );
    }

    public function bannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'banner_player_id',
            ownerKey: Player::primaryKey(),
        );
    }

    public function unbannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'unbanner_player_id',
            ownerKey: Player::primaryKey(),
        );
    }
}
