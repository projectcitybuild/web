<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use App\Domains\Bans\Data\UnbanType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class GamePlayerBan extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;
    use HasStaticTable;

    protected $table = 'game_player_bans';

    protected $fillable = [
        'banned_player_id',
        'banned_alias_at_time',
        'banner_player_id',
        'reason',
        'additional_info',
        'expires_at',
        'created_at',
        'updated_at',
        'unbanned_at',
        'unbanner_player_id',
        'unban_type',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
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
                    return $unbannedAt;
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
            related: MinecraftPlayer::class,
            foreignKey: 'banned_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function bannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'banner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function unbannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'unbanner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function banAppeals(): HasMany
    {
        return $this->hasMany(
            related: BanAppeal::class,
            foreignKey: 'game_ban_id',
            localKey: 'id',
        );
    }

    public function isTemporaryBan(): bool
    {
        return $this->expires_at !== null;
    }

    public function isActive(): bool
    {
        return $this->unbanned_at === null;
    }

    public function getBannerName(): string
    {
        if (is_null($this->bannerPlayer)) {
            return 'System';
        }

        return $this->bannerPlayer->alias ?? 'No Alias';
    }

    public function hasNameChangedSinceBan(): bool
    {
        return $this->banned_alias_at_time !== $this->bannedPlayer->alias;
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.player-bans.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        $player = $this->bannedPlayer->alias
            ?? $this->bannedPlayer->getKey().' player id';

        return "Ban for $player";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('banned_player_id', MinecraftPlayer::class)
            ->addRelationship('banner_player_id', MinecraftPlayer::class)
            ->addRelationship('unbanner_player_id', MinecraftPlayer::class)
            ->add(
                'banned_alias_at_time',
                'reason',
                'expires_at',
                'created_at',
                'updated_at',
                'unbanned_at',
                'unban_type',
            );
    }
}
