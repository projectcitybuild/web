<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

final class PlayerOpElevation extends Model implements AuditableContract, LinkableAuditModel
{
    use Auditable;
    use HasFactory;
    use HasStaticTable;

    protected $table = 'player_op_elevations';
    protected $auditInclude = [
        'player_id',
        'reason',
        'started_at',
        'ended_at',
    ];
    protected $fillable = [
        'player_id',
        'reason',
        'started_at',
        'ended_at',
    ];
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
    public $timestamps = false;

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'player_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return $this->player?->getActivitySubjectLink();
    }

    public function getActivitySubjectName(): ?string
    {
        return $this->player
            ? 'OP elevation for '.$this->player->getActivitySubjectName()
            : "OP elevation #{$this->id}";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('player_id', MinecraftPlayer::class)
            ->add('reason', 'started_at', 'ended_at');
    }

    public function scopeWhereActive(Builder $query)
    {
        $query->where('ended_at', '>', now());
    }
}
