<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MinecraftPlayerAlias extends Model
{
    use HasFactory;

    protected $table = 'players_minecraft_aliases';
    protected $primaryKey = 'players_minecraft_alias_id';
    protected $fillable = [
        'player_minecraft_id',
        'alias',
        'registered_at',
    ];
    protected $hidden = [];
    protected $dates = [
        'registered_at',
        'created_at',
        'updated_at',
    ];

    public function minecraftPlayer(): BelongsTo
    {
        return $this->belongsTo(MinecraftPlayer::class, 'player_minecraft_id', 'player_minecraft_id');
    }
}
