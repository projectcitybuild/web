<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlayerAlias extends Model
{
    use HasFactory;

    protected $table = 'players_aliases';

    protected $primaryKey = 'players_alias_id';

    protected $fillable = [
        'player_minecraft_id',
        'alias',
        'registered_at',
    ];

    protected $dates = [
        'registered_at',
        'created_at',
        'updated_at',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(
            related: Player::class,
            foreignKey: Player::primaryKey(),
            ownerKey: Player::primaryKey(),
        );
    }
}
