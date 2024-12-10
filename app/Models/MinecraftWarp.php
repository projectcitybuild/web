<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MinecraftWarp extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_warps';

    protected $fillable = [
        'name',
        'world',
        'x',
        'y',
        'z',
        'pitch',
        'yaw',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'pitch' => 'decimal:1',
        'yaw' => 'decimal:1',
    ];
}
