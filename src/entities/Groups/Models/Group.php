<?php

namespace Entities\Groups\Models;

use Application\Contracts\Model;

class Group extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    protected $primaryKey = 'group_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'is_default',
        'is_staff',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public $timestamps = false;

}
