<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Database\Factories\MinecraftPlayerWorldTimeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MinecraftPlayerWorldTime extends Model
{
    /** @use HasFactory<MinecraftPlayerWorldTimeFactory> */
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_player_world_times';
    protected $fillable = [
        'player_id',
        'world_uuid',
        'creative_time',
        'survival_time',
        'adventure_time',
        'spectator_time',
    ];
    protected $casts = [
        'creative_time' => 'integer',
        'survival_time' => 'integer',
        'adventure_time' => 'integer',
        'spectator_time' => 'integer',
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
