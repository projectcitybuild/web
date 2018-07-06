<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel {

    /**
     * Returns the name of the table this model uses
     *
     * @return string
     */
    public static function getTableName() : string {
        return with(new static)->getTable();
    }
   
}
