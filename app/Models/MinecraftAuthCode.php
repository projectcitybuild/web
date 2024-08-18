<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class MinecraftAuthCode extends Model
{
    use HasStaticTable;

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
