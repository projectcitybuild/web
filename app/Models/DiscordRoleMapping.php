<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use Database\Factories\DiscordRoleMappingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class DiscordRoleMapping extends Model implements AuditableContract, LinkableAuditModel
{
    /** @use HasFactory<DiscordRoleMappingFactory> */
    use Auditable;
    use HasFactory;

    protected $auditInclude = [
        'guild_id',
        'role_id',
        'discord_role_id',
        'enabled',
    ];
    protected $fillable = [
        'guild_id',
        'role_id',
        'discord_role_id',
        'enabled',
    ];
    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.discord-role-mappings.index');
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->role?->name ?? 'Discord role mapping #'.$this->getKey();
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->add('guild_id', 'discord_role_id')
            ->addRelationship('role_id', Role::class)
            ->addBoolean('enabled');
    }
}
