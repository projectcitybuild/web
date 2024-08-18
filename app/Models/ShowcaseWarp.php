<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShowcaseWarp extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'showcase_warps';

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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'built_at' => 'datetime',
        'location_pitch' => 'decimal:1',
        'location_yaw' => 'decimal:1',
    ];
}
