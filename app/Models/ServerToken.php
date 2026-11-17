<?php

namespace App\Models;

use Altek\Eventually\Eventually;
use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Domains\Auditing\Redactors\MaskedValueRedactor;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class ServerToken extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use Eventually;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'server_tokens';
    protected $auditInclude = [
        'description',
        'token',
        'server_id',
    ];
    protected $attributeModifiers = [
        'token' => MaskedValueRedactor::class,
    ];
    protected $fillable = [
        'token',
        'server_id',
        'description',
        'allowed_ips',
    ];
    public function server(): HasOne
    {
        return $this->hasOne(
            related: Server::class,
            foreignKey: 'server_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.server-tokens.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->description;
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('description', 'token')
            ->addRelationship('server_id', Server::class);
    }
}
