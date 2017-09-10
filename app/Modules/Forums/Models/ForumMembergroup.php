<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class ForumMembergroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_group', 'group_name', 'description', 'online_color', 'min_posts', 'max_messages', 'stars', 'group_type', 'hidden', 'id_parent'
    ];

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
    protected $table = 'membergroups';

    protected $connection = 'mysql_forums';

    public $timestamps = false;
}
