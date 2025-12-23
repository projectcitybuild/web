<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class MinecraftConfig extends Model
{
    use HasFactory;
    use HasStaticTable;
    use SoftDeletes;

    protected $table = 'minecraft_config';
    protected $fillable = [
        'config',
        'version',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public function scopeByLatest(Builder $query)
    {
        $query->orderBy('version', 'desc');
    }
}
