<?php

namespace Entities\Models\Eloquent;

use App\Model;

final class GroupScope extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_scopes';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'scope',
    ];

    public $timestamps = false;
}
