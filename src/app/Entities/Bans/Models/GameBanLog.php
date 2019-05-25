<?php

namespace App\Entities\Bans\Models;

use App\Model;

/**
 * App\Entities\Bans\Models\GameBanLog
 *
 * @property int $game_ban_log_id
 * @property int $game_ban_id Ban record acted upon/created
 * @property int $server_key_id Server key used in the action
 * @property int $ban_action BanActionEnum value
 * @property string|null $incoming_ip IP address of the action creator
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog whereBanAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog whereGameBanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog whereGameBanLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog whereIncomingIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Bans\Models\GameBanLog whereServerKeyId($value)
 * @mixin \Eloquent
 */
class GameBanLog extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_network_ban_logs';

    protected $primaryKey = 'game_ban_log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_ban_id',
        'server_key_id',
        'ban_action',
        'incoming_ip',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'created_at',
    ];

    public $timestamps = false;
}
