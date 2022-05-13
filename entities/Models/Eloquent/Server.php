<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Entities\Models\GameType;
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
        'is_online',
        'num_of_players',
        'num_of_slots',
        'last_queried_at',
    ];

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_queried_at',
    ];

    public function address(): string
    {
        if ($this->ip_alias) {
            return $this->ip_alias;
        }
        if ($this->port && $this->is_port_visible) {
            return $this->ip.':'.$this->port;
        }

        return $this->ip;
    }

    /**
     * Gets the ip address of the server (with port depending on availability).
     *
     * @deprecated
     */
    public function getAddress(): string
    {
        $port = $this->port !== null && $this->is_port_visible
            ? ':'.$this->port
            : '';

        return $this->ip.$port;
    }

    public function gameType(): GameType
    {
        return GameType::tryFrom($this->game_type);
    }
}
