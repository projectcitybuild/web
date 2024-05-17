<?php

namespace App\Models\Eloquent;

use App\Models\MinecraftUUID;
use App\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Player extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'player';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'account_id',
        'last_synced_at',
        'last_seen_at',
    ];

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
            related: PlayerAlias::class,
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

    public function scopeUuid(Builder $query, String $uuid)
    {
        $query->where('uuid', $uuid);
    }
}
