<?php
namespace App\Entities\Warnings\Models;

use App\Model;

/**
 * App\Entities\Warnings\Models\GameWarning
 *
 * @property int $game_warning_id
 * @property int|null $server_id
 * @property int $warned_player_id
 * @property string $warned_player_type Banned player identifier type
 * @property int|null $staff_player_id
 * @property string $staff_player_type Staff player identifier type
 * @property string|null $reason
 * @property int $weight How many points the infraction is worth
 * @property mixed $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $staffPlayer
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $warnedPlayer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereGameWarningId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereStaffPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereStaffPlayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereWarnedPlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereWarnedPlayerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Warnings\Models\GameWarning whereWeight($value)
 * @mixin \Eloquent
 */
class GameWarning extends Model
{
    protected $table = 'game_network_warnings';

    protected $primaryKey = 'game_warning_id';

    protected $fillable = [
        'server_id',
        'warned_player_id',
        'warned_player_type',
        'staff_player_id',
        'staff_player_type',
        'reason',
        'weight',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function warnedPlayer()
    {
        return $this->morphTo(null, 'warned_player_type', 'warned_player_type');
    }

    public function staffPlayer()
    {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }
}
