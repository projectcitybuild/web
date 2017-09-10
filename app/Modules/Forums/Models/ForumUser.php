<?php

namespace App\Modules\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class ForumUser extends Model
{
    protected $primaryKey = 'id_member';

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
    protected $table = 'members';

    protected $connection = 'mysql_forums';

    public $timestamps = false;

    public function UnreadMail()
    {
        return $this->hasMany(\App\Modules\Forums\Models\ForumMailRecipient::class, 'id_member', 'id_member')
            ->where('is_read', 0);
    }
}
