<?php

namespace App\Entities\Players\Models;

use App\Model;

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

    protected $dates = [
        'expires_at',
    ];

}
