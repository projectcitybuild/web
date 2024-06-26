<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class MinecraftAuthCode extends Model
{
    protected $table = 'minecraft_auth_code';

    protected $fillable = [
        'uuid',
        'token',
        'player_minecraft_id',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function player(): HasOne
    {
        return $this->hasOne(
            related: Player::class,
            foreignKey: 'player_minecraft_id',
            localKey: 'player_minecraft_id',
        );
    }
}
