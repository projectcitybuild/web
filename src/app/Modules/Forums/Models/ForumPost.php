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
        $value = str_replace('[b]', '<b>', $value);
        $value = str_replace('[/b]', '</b>', $value);
        $value = str_replace('[i]', '<i>', $value);
        $value = str_replace('[/i]', '</i>', $value);
        $value = str_replace('[hr]', '<hr>', $value);

        $value = preg_replace('/(?:\[img\])(.*)(?:\[\/img\])/', '<img src="$1" width="100%" />', $value);
        $value = preg_replace('/(?:\[url=(.*)\])(.*)(?:\[\/url\])/', '<a href="$1">$2</a>', $value);
        return $value;
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
