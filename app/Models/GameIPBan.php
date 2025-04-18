<?php

namespace App\Models;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Concerns\LogsActivity;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GameIPBan extends Model implements LinkableAuditModel
{
    use HasFactory;
    use HasStaticTable;
    use LogsActivity;

    protected $table = 'game_ip_bans';

    protected $fillable = [
        'banner_player_id',
        'ip_address',
        'reason',
        'created_at',
        'updated_at',
        'unbanned_at',
        'unbanner_player_id',
        'unban_type',
    ];

    protected $casts = [
        'unbanned_at' => 'datetime',
    ];

    public function bannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'banner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function unbannerPlayer(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'unbanner_player_id',
            ownerKey: 'player_minecraft_id',
        );
    }

    public function getActivitySubjectLink(): ?string
    {
        return route('manage.ip-bans.edit', $this);
    }

    public function getActivitySubjectName(): ?string
    {
        return 'IP ban for '.$this->ip_address;
    }

    public function auditAttributeConfig(): AuditAttributes
    {
        return AuditAttributes::build()
            ->addRelationship('banner_player_id', MinecraftPlayer::class)
            ->addRelationship('unbanner_player_id', MinecraftPlayer::class)
            ->add(
                'ip_address',
                'reason',
                'created_at',
                'updated_at',
                'unbanned_at',
                'unban_type',
            );
    }
}
