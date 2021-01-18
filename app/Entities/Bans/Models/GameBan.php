<?php

namespace App\Entities\Bans\Models;

use App\Entities\Servers\Models\Server;
use App\Model;
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
        'banned_player_type',
        'banned_alias_at_time',
        'staff_player_id',
        'staff_player_type',
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

    public function bannedPlayer()
    {
        return $this->morphTo(null, 'banned_player_type', 'banned_player_id');
    }

    public function staffPlayer()
    {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }

    public function unban()
    {
        return $this->belongsTo(GameUnban::class, 'game_ban_id', 'game_ban_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'server_id');
    }

    public function getStaffName()
    {
        if (is_null($this->staffPlayer)) {
            return 'System';
        }

        return $this->staffPlayer->getBanReadableName() ?? 'No Alias';
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
