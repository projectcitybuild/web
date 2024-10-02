<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;

/** @deprecated */
final class ServerTokenScope extends Model
{
    use HasStaticTable;

    protected $table = 'server_token_scopes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'scope',
    ];

    public $timestamps = false;
}
