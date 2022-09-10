<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @deprecated
 */
final class GameUnban extends Model
{
    use HasFactory;

    protected $table = 'game_network_unbans';
    protected $primaryKey = 'game_unban_id';
    protected $fillable = [
        'game_ban_id',
        'staff_player_id',
    ];
    protected $hidden = [

    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function ban(): BelongsTo
    {
        return $this->belongsTo(GameBan::class, 'game_ban_id', 'game_ban_id');
    }

    public function staffPlayer(): BelongsTo
    {
        return $this->belongsTo(MinecraftPlayer::class, 'staff_player_id', 'player_minecraft_id');
    }
}
