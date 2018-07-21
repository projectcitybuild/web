<?php

namespace Application\Modules\Servers\Models;

use Application\Model;

class ServerCategory extends Model
{
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
        return $this->hasMany('Application\Modules\Servers\Models\Server', 'server_category_id', 'server_category_id');
    }
}
