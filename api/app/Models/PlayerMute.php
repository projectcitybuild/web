<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder forPlayer(Player|Model $player)
 */
final class PlayerMute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'player_mute';

    protected $fillable = [
        'muted_player_id',
        'muter_player_id',
    ];

    public function scopeForPlayer(Builder $query, Player $player)
    {
        $query->where('muted_player_id', $player->getKey());
    }

    public function mutedPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'muted_player_id',
            ownerKey: Player::primaryKey(),
        );
    }

    public function muterPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'muter_player_id',
            ownerKey: Player::primaryKey(),
        );
    }
}
