<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    protected $primaryKey = 'id_topic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
    protected $table = 'topics';

    protected $connection = 'mysql_forums';

    public $timestamps = false;


    public function firstPost() {
        return $this->hasOne('App\Modules\Forums\Models\ForumPost', 'id_msg', 'id_first_msg');
    }

    public function lastPost() {
        return $this->hasOne('App\Modules\Forums\Models\ForumPost', 'id_msg', 'id_last_msg');
    }

    public function poster() {
        return $this->hasOne('App\Modules\Forums\Models\ForumUser', 'id_member', 'id_member_started');
    }

    public function board() {
        return $this->belongsTo('App\Modules\Forums\Models\ForumBoard', 'id_board', 'id_board');
    }
}
