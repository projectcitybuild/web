<?php

namespace App\Models;

use App\Core\Data\GameType;
use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\CausesActivity;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use App\Domains\ServerStatus\Data\ServerQueryResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

final class Server extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;
    use CausesActivity;

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

    protected $casts = [
        'last_queried_at' => 'datetime',
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

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.servers.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->name;
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('name', 'ip', 'ip_alias', 'port', 'game_type', 'display_order')
            ->addBoolean('is_port_visible', 'is_querying', 'is_visible');
    }

    public function updateWithStatus(ServerQueryResult $status, Carbon $queriedAt): void
    {
        if ($status->isOnline) {
            $this->num_of_players = $status->numOfPlayers;
            $this->num_of_slots = $status->numOfSlots;
        } else {
            $this->num_of_players = 0;
            $this->num_of_slots = 0;
        }
        $this->is_online = $status->isOnline;
        $this->last_queried_at = $queriedAt;
        $this->save();
    }
}
