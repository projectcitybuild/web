<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Permission extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'permissions';
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(
            related: Role::class,
            table: 'roles_permissions'
        );
    }
}
