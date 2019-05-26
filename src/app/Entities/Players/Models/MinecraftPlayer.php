<?php

namespace App\Entities\Players\Models;

use App\Model;
use App\Entities\Bans\BannableModelInterface;
use App\Entities\Accounts\Models\Account;

/**
 * App\Entities\Players\Models\MinecraftPlayer
 *
 * @property int $player_minecraft_id
 * @property string $uuid
 * @property int|null $account_id
 * @property int $playtime Total playtime in minutes
 * @property \Illuminate\Support\Carbon $last_seen_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Accounts\Models\Account[] $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Players\Models\MinecraftPlayerAlias[] $aliases
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer wherePlayerMinecraftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer wherePlaytime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Players\Models\MinecraftPlayer whereUuid($value)
 * @mixin \Eloquent
 */
class MinecraftPlayer extends Model implements BannableModelInterface
{
    protected $table = 'players_minecraft';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'account_id',
        'playtime',
        'last_seen_at',
    ];

    protected $hidden = [
        
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_seen_at',
    ];


    /**
     * {@inheritDoc}
     */
    public function getBanIdentifier(): string
    {
        return $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function getBanReadableName(): string
    {
        $alias = $this->belongsTo(MinecraftPlayerAlias::class, 'player_minecraft_id', 'player_minecraft_id')->latest();

        return $alias !== null
            ? $alias->alias
            : '';
    }

    
    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

    public function aliases()
    {
        return $this->hasMany(MinecraftPlayerAlias::class, 'player_minecraft_id', 'player_minecraft_id');
    }
}
