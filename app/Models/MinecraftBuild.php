<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MinecraftBuild extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_builds';
    protected $auditInclude = [
        'player_id',
        'name',
        'description',
        'lore',
        'world',
        'x',
        'y',
        'z',
        'pitch',
        'yaw',
    ];
    protected $fillable = [
        'player_id',
        'name',
        'description',
        'lore',
        'votes',
        'world',
        'x',
        'y',
        'z',
        'pitch',
        'yaw',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'pitch' => 'decimal:1',
        'yaw' => 'decimal:1',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'player_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return null;
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->name ? "Build: {$this->name}" : "Build #{$this->id}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('player_id', MinecraftPlayer::class)
            ->add('name', 'description', 'lore', 'world', 'x', 'y', 'z', 'pitch', 'yaw');
    }
}
