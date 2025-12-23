<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\CausesActivity;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Server extends Model implements LinkableAuditModel
{
    use CausesActivity;
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'servers';
    protected $primaryKey = 'server_id';
    protected $fillable = [
        'name',
        'ip',
        'ip_alias',
        'port',
        'web_port',
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

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.servers.edit', $this);
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
}
