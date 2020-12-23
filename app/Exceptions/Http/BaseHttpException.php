<?php

namespace App\Exceptions\Http;

abstract class BaseHttpException extends \Exception
{
    protected string $id;

    protected int $status;

    public function __construct($id, $message)
    {
        $this->id = $id;

        parent::__construct($message);
    }

    /**
     * Returns the unique identifier for this exception
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns a HTTP code (eg. 400, 404)
     */
    public function getStatusCode(): void
    {
        return $this->status;
    }
}
