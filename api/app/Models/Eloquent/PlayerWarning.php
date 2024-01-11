<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerWarning extends Model
{
    use HasFactory;

    protected $table = 'player_warnings';

    protected $fillable = [
        'warned_player_id',
        'warner_player_id',
        'reason',
        'additional_info',
        'weight',
        'created_at',
        'updated_at',
        'acknowledged_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'acknowledged_at',
    ];

    public function warnedPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'warned_player_id',
            ownerKey: Player::primaryKey(),
        );
    }

    public function warnerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: 'warner_player_id',
            ownerKey: Player::primaryKey(),
        );
    }
}
