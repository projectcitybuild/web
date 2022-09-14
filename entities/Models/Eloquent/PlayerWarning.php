<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'is_acknowledged',
        'created_at',
        'updated_at',
        'acknowledged_at',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'acknowledged_at',
    ];
    protected $casts = [
        'is_acknowledged' => 'boolean',
    ];

    public function warnedPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warned_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function warnerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }
}
