<?php

namespace App\Entities\Servers\Models;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class ServerCategory extends Model
{
    use HasFactory;

    protected $table = 'server_categories';

    protected $primaryKey = 'server_category_id';

    protected $fillable = [
        'name',
        'display_order',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function servers()
    {
        return $this->hasMany('App\Entities\Servers\Models\Server', 'server_category_id', 'server_category_id');
    }
}
