<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class UserAlias extends Model {
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_aliases';

    protected $primaryKey = 'user_alias_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_alias_type_id',
        'user_id',
        'alias',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    
    public function aliasType() {
        return $this->hasOne('App\Modules\Users\Models\UserAliasType', 'user_alias_type_id', 'user_alias_type_id');
    }
}
