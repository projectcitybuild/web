<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class UserAliasType extends Model {
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_alias_types';

    protected $primaryKey = 'user_alias_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

}
