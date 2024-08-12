<?php

namespace Entities\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class MinecraftAuthCode extends Model
{
    protected $table = 'minecraft_auth_codes';

    protected $primaryKey = 'minecraft_auth_code_id';

    protected $fillable = [
        'uuid',
        'token',
        'player_minecraft_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function minecraftPlayer(): HasOne
    {
        return $this->hasOne(MinecraftPlayer::class, 'player_minecraft_id', 'player_minecraft_id');
    }
}
