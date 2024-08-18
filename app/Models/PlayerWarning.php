<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerWarning extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'player_warnings';

    protected $fillable = [
        'warned_player_id',
        'warner_player_id',
        'reason',
        'additional_info',
        'weight',
        'is_acknowledged',
        'created_at',
        'updated_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'is_acknowledged' => 'boolean',
    ];

    public function warnedPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warned_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function warnerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'warner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('front.panel.warnings.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        $player = $this->warnedPlayer->currentAlias()?->alias
            ?? $this->warnedPlayer->getKey().' player id';

        return "Warning for $player";
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addBoolean('is_acknowledged')
            ->addMultiline('reason', 'additional_info')
            ->addRelationship('warned_player_id', MinecraftPlayer::class)
            ->addRelationship('warner_player_id', MinecraftPlayer::class)
            ->add(
                'weight',
                'created_at',
                'updated_at',
                'acknowledged_at',
            );
    }
}
