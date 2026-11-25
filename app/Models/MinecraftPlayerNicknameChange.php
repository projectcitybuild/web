<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Database\Factories\MinecraftPlayerNicknameChangeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MinecraftPlayerNicknameChange extends Model
{
    /** @use HasFactory<MinecraftPlayerNicknameChangeFactory> */
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_player_nickname_changes';
    protected $fillable = [
        'player_id',
        'new_nickname',
        'changed_at',
    ];
    protected $casts = [
        'changed_at' => 'datetime',
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
