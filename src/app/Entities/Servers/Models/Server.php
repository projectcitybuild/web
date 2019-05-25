<?php

namespace App\Entities\Servers\Models;

use App\Model;
use App\Entities\GameType;

/**
 * App\Entities\Servers\Models\Server
 *
 * @property int $server_id
 * @property int $server_category_id
 * @property string $name
 * @property string $ip
 * @property string|null $ip_alias An alternative address to connect to the server
 * @property string|null $port
 * @property int $game_type Type of game server, used to determine an adapter to use for status querying
 * @property mixed $is_port_visible Whether the port will be displayed
 * @property mixed $is_querying
 * @property mixed $is_visible Whether the server is visible in the server feed
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Servers\Models\ServerCategory $category
 * @property-read \App\Entities\Servers\Models\ServerStatus $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereGameType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereIpAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereIsPortVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereIsQuerying($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereServerCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\Server whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
    protected $table = 'servers';

    protected $primaryKey = 'server_id';

    protected $fillable = [
        'name',
        'server_category_id',
        'ip',
        'ip_alias',
        'port',
        'is_port_visible',
        'is_querying',
        'is_visible',
        'display_order',
        'game_type',
    ];

    protected $hidden = [
        
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Gets the ip address of the server (with port depending on availability)
     *
     * @return string
     */
    public function getAddress() : string
    {
        $port = $this->port != null && $this->is_port_visible
            ? ':'.$this->port
            : '';

        return $this->ip . $port;
    }


    public function category()
    {
        return $this->hasOne('App\Entities\Servers\Models\ServerCategory', 'server_category_id', 'server_category_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Entities\Servers\Models\ServerStatus', 'server_id', 'server_id')
            ->take(1)
            ->latest();
    }
    

    public function isOnline()
    {
        return $this->status && $this->status->is_online;
    }

    public function gameType() : GameType
    {
        switch ($this->game_type) {
            case GameType::Minecraft:
                return new GameType(GameType::Minecraft);
            case GameType::Terraria:
                return new GameType(GameType::Terraria);
            case GameType::Starbound:
                return new GameType(GameType::Starbound);
            default:
                throw new \InvalidArgumentException();
        }
    }
}
