<?php

namespace App\Entities\Eloquent\Servers\Models;

use App\Model;
use App\Entities\GameType;

final class Server extends Model
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
        return $this->hasOne('App\Entities\Eloquent\Servers\Models\ServerCategory', 'server_category_id', 'server_category_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Entities\Eloquent\Servers\Models\ServerStatus', 'server_id', 'server_id')
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
