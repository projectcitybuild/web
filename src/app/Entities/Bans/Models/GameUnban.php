<?php

namespace App\Entities\Bans\Models;

use App\Model;

/**
 * App\Entities\Bans\Models\GameUnban
 *
 * @property int $game_unban_id
 * @property int $game_ban_id
 * @property int|null $staff_player_id
 * @property string $staff_player_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Bans\Models\GameBan $ban
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $staffPlayer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban whereGameBanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban whereGameUnbanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban whereStaffPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban whereStaffPlayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameUnban whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GameUnban extends Model
{
    protected $table = 'game_network_unbans';

    protected $primaryKey = 'game_unban_id';

    protected $fillable = [
        'game_ban_id',
        'staff_player_id',
        'staff_player_type',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function ban()
    {
        return $this->hasOne('App\Entities\Bans\Models\GameBan', 'game_ban_id', 'game_ban_id');
    }

    public function staffPlayer()
    {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }
}
