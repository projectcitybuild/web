<?php

namespace App\Core\Utilities\Traits;

trait HasStaticTable
{
    public static function tableName(): string
    {
        return with(new static)->getTable();
    }

    public static function primaryKey(): string
    {
        return with(new static)->primaryKey;
    }
}
