<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $primaryKey = 'id_msg';

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
    protected $table = 'messages';

    protected $connection = 'mysql_forums';

    public $timestamps = false;

    protected $dates = [
        'poster_time'
    ];
    
    public function getBodyAttribute($value) {
        $bbcodeParser = new \Golonka\BBCode\BBCodeParser;
        return $bbcodeParser->parseCaseInsensitive($value);
    }


    public function poster() {
        return $this->hasOne('App\Modules\Forums\Models\ForumUser', 'id_member', 'id_member');
    }

    public function topic() {
        return $this->belongsTo('App\Modules\Forums\Models\ForumTopic', 'id_topic', 'id_topic');
    }

    public function board() {
        return $this->belongsTo('App\Modules\Forums\Models\ForumBoard', 'id_board', 'id_board');
    }
}
