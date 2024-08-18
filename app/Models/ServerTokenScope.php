<?php

namespace App\Models;

use App\Model;

final class ServerTokenScope extends Model
{
    protected $table = 'server_token_scopes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'scope',
    ];

    public $timestamps = false;
}
