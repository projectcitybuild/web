<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MinecraftPlayerSession extends Model
{
    use HasFactory;
    use HasStaticTable;
    use Prunable;

    protected $table = 'minecraft_player_sessions';
    protected $fillable = [
        'player_id',
        'seconds',
        'starts_at',
        'ends_at',
    ];
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
    public $timestamps = false;

    public function prunable(): Builder
    {
        return self::where('ends_at', '<=', now()->subMonth());
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'player_id',
            ownerKey: MinecraftPlayer::primaryKey(),
        );
    }
}
