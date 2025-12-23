<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
