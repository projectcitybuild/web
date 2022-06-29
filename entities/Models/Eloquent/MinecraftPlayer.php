<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Shared\PlayerLookup\Contracts\Player;

/**
 * @property string uuid
 * @property int account_id
 * @property ?Account account
 * @property ?Carbon last_synced_at
 * @property ?Carbon last_seen_at
 * @property Collection aliases
 */
final class MinecraftPlayer extends Model implements Player
{
    use HasFactory;

    protected $table = 'players_minecraft';
    protected $primaryKey = 'player_minecraft_id';
    protected $fillable = [
        'uuid',
        'account_id',
        'last_synced_at',
        'last_seen_at',
    ];
    protected $hidden = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'last_synced_at',
    ];

    public function getBanReadableName(): ?string
    {
        $aliases = $this->aliases;
        if ($aliases->count() == 0) {
            return null;
        }

        return $this->aliases->last()->alias;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(MinecraftPlayerAlias::class, 'player_minecraft_id', 'player_minecraft_id');
    }

    public function gameBans()
    {
        return $this->morphMany(GameBan::class, 'banned_player');
    }

    public function isBanned()
    {
        return $this->gameBans()->active()->exists();
    }

    public function banAppeals()
    {
        // We have to do this because game bans are a polymorphic relationship, but this is just what
        // HasManyThrough does internally anyway..
        return BanAppeal::whereIn('game_ban_id', $this->gameBans()->pluck('game_ban_id'));
    }

    /**
     * Update the last seen at time of the player to now.
     */
    public function touchLastSyncedAt(): bool
    {
        $this->last_synced_at = $this->freshTimestamp();

        return $this->save();
    }

    public function hasAlias(string $alias): bool
    {
        return $this->aliases
            ->where('alias', $alias)
            ->isNotEmpty();
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

    public function getLinkedAccount(): ?Account
    {
        return $this->account;
    }
}
