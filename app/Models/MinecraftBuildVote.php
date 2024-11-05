<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MinecraftBuildVote extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'minecraft_build_votes';

    protected $fillable = [
        'build_id',
        'player_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'pitch' => 'decimal:1',
        'yaw' => 'decimal:1',
    ];
}
