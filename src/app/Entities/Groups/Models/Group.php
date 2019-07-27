<?php

namespace App\Entities\Groups\Models;

use App\Model;

/**
 * App\Entities\Groups\Models\Group
 *
 * @property int $group_id
 * @property string $name
 * @property string|null $alias
 * @property mixed $is_default
 * @property mixed $is_staff
 * @property mixed $is_admin
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group whereIsStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Groups\Models\Group whereName($value)
 * @mixin \Eloquent
 */
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
