<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class Server extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'servers';
    protected $fillable = [
        'name',
        'ip',
        'ip_alias',
        'port',
    ];
    protected $auditInclude = [
        'name',
        'ip',
        'ip_alias',
        'port',
    ];

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
