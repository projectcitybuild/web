<?php

namespace App\Models\Eloquent;

use Database\Factories\ServerCategoryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ServerCategory extends Model
{
    use HasFactory;

    protected $table = 'server_categories';

    protected $primaryKey = 'server_category_id';

    protected $fillable = [
        'name',
        'display_order',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected static function newFactory(): Factory
    {
        return ServerCategoryFactory::new();
    }

    public function servers(): HasMany
    {
        return $this->hasMany(
            related: Server::class,
            foreignKey: 'server_category_id',
            localKey: 'server_category_id',
        );
    }
}
