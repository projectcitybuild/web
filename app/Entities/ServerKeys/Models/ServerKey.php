<?php

namespace App\Entities\ServerKeys\Models;

use App\Model;

final class ServerKey extends Model
{
    /**
     * The table associated with the model.
     */
    protected string $table = 'server_keys';

    protected $primaryKey = 'server_key_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'server_id',
        'token',
        'can_local_ban',
        'can_global_ban',
        'can_warn',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected array $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function server()
    {
        return $this->hasOne('App\Entities\Servers\Models\Server', 'server_id', 'server_id');
    }
}
