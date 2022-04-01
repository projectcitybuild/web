<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 */
abstract class Repository
{
    /**
     * Namespace that resolves to a model.
     * Each repository should override this property.
     *
     * @var string
     */
    protected $model;

    /**
     * @var Model
     */
    private $instance;

    /**
     * Returns an instance of the model.
     */
    public function getModel(): Model
    {
        if (! isset($this->instance)) {
            $this->instance = resolve($this->model);
        }

        return $this->instance;
    }
}
