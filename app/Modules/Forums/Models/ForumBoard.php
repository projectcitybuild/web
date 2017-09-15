<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class ForumBoard extends Model
{
    protected $primaryKey = 'id_board';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = [];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'boards';

    protected $connection = 'mysql_forums';

    public $timestamps = false;

    public function PermissionProfile() {
        return $this->hasOne('App\Modules\Forums\Models\ForumPermissionProfile', 'id_profile', 'id_profile');
    }

    public function Permissions() {
        return $this->hasMany('App\Modules\Forums\Models\ForumBoardPermission', 'id_profile', 'id_profile');
    }
}
