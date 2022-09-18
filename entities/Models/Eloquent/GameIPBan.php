<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GameIPBan extends Model
{
    use HasFactory;

    protected $table = 'game_ip_bans';
    protected $fillable = [
        'banner_player_id',
        'ip_address',
        'reason',
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
        'unbanned_at' => 'datetime',
    ];

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
}
