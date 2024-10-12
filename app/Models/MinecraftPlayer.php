<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class MinecraftPlayer extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'players_minecraft';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'alias',
        'account_id',
        'last_synced_at',
        'last_seen_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
            ownerKey: 'account_id',
        );
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(
            related: MinecraftPlayerAlias::class,
            foreignKey: 'player_minecraft_id',
            localKey: 'player_minecraft_id',
        );
    }

    public function gamePlayerBans(): HasMany
    {
        return $this->hasMany(
            related: GamePlayerBan::class,
            foreignKey: 'banned_player_id',
            localKey: 'player_minecraft_id',
        );
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(
            related: PlayerWarning::class,
            foreignKey: 'warned_player_id',
            localKey: 'player_minecraft_id',
        );
    }

    public function isBanned()
    {
        return $this->gamePlayerBans()->active()->exists();
    }

    public function banAppeals()
    {
        // We have to do this because game bans are a polymorphic relationship, but this is just what
        // HasManyThrough does internally anyway..
        return BanAppeal::whereIn('game_ban_id', $this->gamePlayerBans()->pluck('id'));
    }

    /**
     * Update the last seen at time of the player to now.
     */
    public function touchLastSyncedAt(): bool
    {
        $this->last_synced_at = $this->freshTimestamp();

        return $this->save();
    }

    public static function firstOrCreate(MinecraftUUID $uuid, ?string $alias = null): self
    {
        $existing = self::whereUuid($uuid)->first();
        if ($existing->alias !== $alias) {
            $existing->alias = $alias;
            $existing->save();
        }
        return self::create([
            'uuid' => $uuid->trimmed(),
            'alias' => $alias,
        ]);
    }

    public function scopeWhereUuid(Builder $query, MinecraftUUID $uuid)
    {
        $query->where('uuid', $uuid->trimmed());
    }

    /** ************************************************
     *
     * GamePlayable
     *
     ***************************************************/
    public function getRawModel(): static
    {
        return $this;
    }

    /** @deprecated */
    public function getLinkedAccount(): ?Account
    {
        return $this->account;
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('account_id', Account::class);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.minecraft-players.show', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->alias ?? Str::limit($this->uuid, 10);
    }
}
