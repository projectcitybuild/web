<?php

namespace App\Models\Eloquent;

use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShowcaseWarp extends Model
{
    use HasFactory;

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
    
    protected $dates = [
        'built_at',
    ];

    protected $casts = [
        'location_pitch' => 'decimal:1',
        'location_yaw' => 'decimal:1',
    ];
}
