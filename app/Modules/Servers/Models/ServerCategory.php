<?php

namespace App\Modules\Servers\Models;

use Illuminate\Database\Eloquent\Model;

class ServerCategory extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_categories';

    protected $primaryKey = 'server_category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_order',
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


    public function servers() {
        return $this->hasMany('App\Modules\Servers\Models\Server', 'server_category_id', 'server_category_id');
    }
}
