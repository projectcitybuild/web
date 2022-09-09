<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Domain\Bans\UnbanType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

final class GameBan extends Model
{
    use Searchable, HasFactory;

    protected $table = 'game_network_bans';
    protected $primaryKey = 'game_ban_id';
    protected $fillable = [
        'server_id',
        'banned_player_id',
        'banned_alias_at_time',
        'staff_player_id',
        'reason',
        'is_active',
        'is_global_ban',
        'expires_at',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [];
    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'unban_type' => UnbanType::class,
    ];

    public function scopeActive(Builder $query)
    {
        $query->whereNull('unbanned_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', now());
            });
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

    public function banAppeals()
    {
        return $this->hasMany(BanAppeal::class, 'game_ban_id', 'game_ban_id');
    }

    public function isTemporaryBan(): bool
    {
        return $this->expires_at !== null;
    }

    public function getStaffName()
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
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'game_ban_id' => $this->game_ban_id,
            'banned_alias_at_time' => $this->banned_alias_at_time,
            'reason' => $this->reason,
        ];
    }
}
