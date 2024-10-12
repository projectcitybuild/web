<?php

namespace App\Models;

use Altek\Eventually\Eventually;
use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class ServerToken extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;
    use Eventually;

    protected $table = 'server_tokens';

    protected $fillable = [
        'token',
        'server_id',
        'description',
    ];

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];

    public function server(): HasOne
    {
        return $this->hasOne(
            related: Server::class,
            foreignKey: 'server_id',
            localKey: 'server_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.server-tokens.edit', $this);
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
