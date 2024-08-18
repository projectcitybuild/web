<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ServerCategory extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'server_categories';

    protected $primaryKey = 'server_category_id';

    protected $fillable = [
        'name',
        'display_order',
    ];

    public function servers(): HasMany
    {
        return $this->hasMany('App\Models\Server', 'server_category_id', 'server_category_id');
    }
}
