<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    /**
     * Namespace that resolves to a model.
     * Each repository should override this property.
     */
    protected string $model;

    private Model $instance;

    /**
     * Returns an instance of the model
     */
    public function getModel(): Model
    {
        if (! isset($this->instance)) {
            $this->instance = resolve($this->model);
        }
        return $this->instance;
    }
}
