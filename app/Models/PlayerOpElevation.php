<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerOpElevation extends Model
{
    use HasStaticTable;

    protected $table = 'player_op_elevations';
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
            ownerKey: MinecraftPlayer::primaryKey(),
        );
    }
}
