<?php

namespace App\Entities\Servers\Models;

use App\Model;

/**
 * App\Entities\Servers\Models\ServerCategory
 *
 * @property int $server_category_id
 * @property string $name
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Servers\Models\Server[] $servers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory whereServerCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Servers\Models\ServerCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServerCategory extends Model
{
    protected $table = 'server_categories';

    protected $primaryKey = 'server_category_id';

    protected $fillable = [
        'name',
        'display_order',
    ];

    protected $hidden = [
        
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];


    public function servers()
    {
        return $this->hasMany('App\Entities\Servers\Models\Server', 'server_category_id', 'server_category_id');
    }
}
