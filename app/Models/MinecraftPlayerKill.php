<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Database\Factories\MinecraftPlayerKillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MinecraftPlayerKill extends Model
{
    /** @use HasFactory<MinecraftPlayerKillFactory> */
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_player_kills';
    protected $fillable = [
        'event_id',
        'player_id',
        'victim_player_id',
        'weapon',
        'killed_at',
    ];
    protected $casts = [
        'killed_at' => 'datetime',
    ];
    public $timestamps = false;

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'player_id',
        );
    }

    public function victim(): BelongsTo
    {
        return $this->belongsTo(
            related: MinecraftPlayer::class,
            foreignKey: 'victim_player_id',
        );
    }
}
