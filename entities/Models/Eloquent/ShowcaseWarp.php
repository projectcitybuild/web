<?php

namespace Entities\Models\Eloquent;

use App\Model;

class ShowcaseWarp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'showcase_warps';

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'title',
        'description',
        'creators',
        'location_world',
        'location_x',
        'location_y',
        'location_z',
        'location_pitch',
        'location_yaw',
        'built_at',
    ];
}
