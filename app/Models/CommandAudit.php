<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CommandAudit extends Model
{
    use HasUuids;
    use HasStaticTable;

    protected $table = 'audit_commands';
    protected $fillable = [
        'command',
        'actor',
        'player_id',
        'ip',
        'created_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];
    public $timestamps = false;

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'player_id',
        );
    }
}
