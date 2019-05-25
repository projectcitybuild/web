<?php
namespace App;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * App\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model query()
 * @mixin \Eloquent
 */
class Model extends EloquentModel
{
    /**
     * Returns the name of the table this model uses
     *
     * @return string
     */
    public static function getTableName() : string
    {
        return with(new static)->getTable();
    }
}
