<?php

namespace App\Entities\ServerKeys\Models;

use App\Model;

/**
 * App\Entities\ServerKeys\Models\ServerKey
 *
 * @property int $server_key_id
 * @property int $server_id The only server this key has access to
 * @property string $token
 * @property mixed $can_local_ban Whether this key can create bans that affect only the server the player was banned on
 * @property mixed $can_global_ban Whether this key can create bans that affect every PCB service
 * @property mixed $can_warn
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Servers\Models\Server $server
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereCanGlobalBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereCanLocalBan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereCanWarn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereServerKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\ServerKeys\Models\ServerKey whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServerKey extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_keys';

    protected $primaryKey = 'server_key_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'server_id',
        'token',
        'can_local_ban',
        'can_global_ban',
        'can_warn',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];


    public function server()
    {
        return $this->hasOne('App\Entities\Servers\Models\Server', 'server_id', 'server_id');
    }
}
