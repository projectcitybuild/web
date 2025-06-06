<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinecraftHome extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_homes';

    protected $fillable = [
        'player_id',
        'name',
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
            ownerKey: MinecraftPlayer::primaryKey(),
        );
    }
}
