<?php
namespace App\Core\Exceptions;

abstract class BaseHttpException extends \Exception {

    /**
     * @var string
     */
    protected $id;

    /**
     * Returns the unique identifier for this exception
     *
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @var integer
     */
    protected $status;

    /**
     * Returns a HTTP code (eg. 400, 404)
     *
     * @return void
     */
    public function getStatusCode() {
        return $this->status;
    }

    public function __construct($id, $message) {
        $this->id = $id;

        parent::__construct($message);
    }
}