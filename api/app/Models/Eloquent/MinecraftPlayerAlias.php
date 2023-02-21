<?php

namespace App\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

final class MinecraftPlayerAlias extends Model
{
    use HasFactory;
    use Searchable;

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

    public function toSearchableArray()
    {
        return [
            'players_minecraft_alias_id' => $this->getKey(),
            'alias' => $this->alias,
            'player_id' => $this->minecraftPlayer->getKey(),
        ];
    }
}
