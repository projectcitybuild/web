<?php

namespace App\Entities\Models\Eloquent;

use App\Model;

/**
 * @deprecated Use Laravel Sanctum to issue API tokens instead
 */
final class ServerKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_keys';

    protected $primaryKey = 'server_key_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function server()
    {
        return $this->hasOne('App\Entities\Models\Eloquent\Server', 'server_id', 'server_id');
    }
}
