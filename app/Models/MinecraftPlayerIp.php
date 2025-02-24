<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinecraftPlayerIp extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_player_ips';

    protected $fillable = [
        'player_id',
        'ip',
        'created_at',
        'updated_at',
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
