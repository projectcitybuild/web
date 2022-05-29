<?php

namespace Entities\Models\Eloquent;

use App\Model;

final class ServerTokenScope extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_token_scopes';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'scope',
    ];

    public $timestamps = false;
}
