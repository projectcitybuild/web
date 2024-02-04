<?php

namespace App;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * App\Model.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Model query()
 *
 * @mixin \Eloquent
 */
class Model extends EloquentModel
{
    /**
     * Returns the name of the table this model uses.
     */
    public static function getTableName(): string
    {
        /** @phpstan-ignore-next-line */
        return with(new static())->getTable();
    }
}
