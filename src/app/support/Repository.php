<?php
namespace App\Support;

use Illuminate\Database\Eloquent\Model;


abstract class Repository {

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
     * Returns an instance of the model
     *
     * @return Model
     */
    public function getModel() : Model {
        if(!isset($this->instance)) {
            $this->instance = resolve($this->model);
        }
        return $this->instance;
    }

}