<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Carbon\Carbon;
use Domain\Bans\UnbanType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

final class GameIPBan extends Model
{
    use Searchable, HasFactory;

    protected $table = 'game_player_bans';
    protected $primaryKey = 'game_ban_id';
    protected $fillable = [
        'server_id',
        'banned_player_id',
        'banned_alias_at_time',
        'staff_player_id',
        'reason',
        'expires_at',
        'created_at',
        'updated_at',
        'unbanned_at',
        'unbanner_player_id',
        'unban_type',
    ];
    protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'expires_at',
        'unbanned_at' => 'datetime',
    ];

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
        return $this->belongsTo(MinecraftPlayer::class, 'banned_player_id', 'player_minecraft_id');
    }

    public function staffPlayer(): BelongsTo
    {
        return $this->belongsTo(MinecraftPlayer::class, 'staff_player_id', 'player_minecraft_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'server_id');
    }

    public function banAppeals(): HasMany
    {
        return $this->hasMany(BanAppeal::class, 'game_ban_id', 'game_ban_id');
    }

    public function isTemporaryBan(): bool
    {
        return $this->expires_at !== null;
    }

    public function isActive(): bool
    {
        return $this->unbanned_at === null;
    }

    public function getStaffName(): string
    {
        if (is_null($this->staffPlayer)) {
            return 'System';
        }

        return $this->staffPlayer->getBanReadableName() ?? 'No Alias';
    }

    public function hasNameChangedSinceBan(): bool
    {
        return $this->banned_alias_at_time !== $this->bannedPlayer->getBanReadableName();
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'game_ban_id' => $this->game_ban_id,
            'banned_alias_at_time' => $this->banned_alias_at_time,
            'reason' => $this->reason,
        ];
    }
}
