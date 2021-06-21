<?php

namespace App\Entities\Servers\Models;

use App\Entities\GameType;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Server extends Model
{
    use HasFactory;

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

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function address(): string
    {
        if ($this->ip_alias) {
            return $this->ip_alias;
        }
        if ($this->port && $this->is_port_visible) {
            return $this->ip . ':' . $this->port;
        }
        return $this->ip;
    }

    /**
     * Gets the ip address of the server (with port depending on availability).
     * @deprecated
     */
    public function getAddress(): string
    {
        $port = $this->port !== null && $this->is_port_visible
            ? ':'.$this->port
            : '';

        return $this->ip.$port;
    }

    public function serverCategory(): BelongsTo
    {
        return $this->belongsTo(ServerCategory::class, 'server_category_id', 'server_category_id');
    }

    public function status()
    {
        return $this->belongsTo(ServerStatus::class, 'server_id', 'server_id')
            ->take(1)
            ->latest();
    }

    public function isOnline()
    {
        return $this->status && $this->status->is_online;
    }

    public function gameType(): GameType
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
