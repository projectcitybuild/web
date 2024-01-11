<?php

namespace App\Models\Eloquent;

use App\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Player extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'players';

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
        'last_seen_at',
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

    public function bans(): HasMany
    {
        return $this->hasMany(
            related: PlayerBan::class,
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

//    public function banAppeals()
//    {
//        // We have to do this because game bans are a polymorphic relationship, but this is just what
//        // HasManyThrough does internally anyway..
//        return BanAppeal::whereIn('game_ban_id', $this->gamePlayerBans()->pluck('id'));
//    }

//    /**
//     * Update the last seen at time of the player to now.
//     */
//    public function touchLastSyncedAt(): bool
//    {
//        $this->last_synced_at = $this->freshTimestamp();
//
//        return $this->save();
//    }
//
//    public function hasAlias(string $alias): bool
//    {
//        return $this->aliases
//            ->where('alias', $alias)
//            ->isNotEmpty();
//    }
}
