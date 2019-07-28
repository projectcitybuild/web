<?php

namespace App\Entities\Bans\Models;

use App\Model;
use Laravel\Scout\Searchable;

/**
 * App\Entities\Bans\Models\GameBan
 *
 * @property int $game_ban_id
 * @property int|null $server_id
 * @property int $banned_player_id
 * @property string $banned_player_type Banned player identifier type
 * @property string $banned_alias_at_time Alias of the player at ban time for logging purposes
 * @property int|null $staff_player_id
 * @property string $staff_player_type Staff player identifier type
 * @property string|null $reason
 * @property mixed $is_active Whether the ban is active
 * @property mixed $is_global_ban Whether this player is banned on all PCB servers, not just the server they were banned on
 * @property \Illuminate\Support\Carbon|null $expires_at Date that this ban auto-expires on
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $bannedPlayer
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $staffPlayer
 * @property-read \App\Entities\Bans\Models\GameUnban $unban
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereBannedAliasAtTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereBannedPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereBannedPlayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereGameBanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereIsGlobalBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereStaffPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereStaffPlayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GameBan extends Model
{
    use Searchable;

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

    protected $hidden = [

    ];

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
        return $this->belongsTo('App\Entities\Bans\Models\GameUnban', 'game_ban_id', 'game_ban_id');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = [
            'game_ban_id' => $this->game_ban_id,
            'banned_alias_at_time' => $this->banned_alias_at_time,
            'reason' => $this->reason
        ];

        return $array;
    }
}
