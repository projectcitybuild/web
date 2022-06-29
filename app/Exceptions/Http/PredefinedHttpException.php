<?php

namespace App\Exceptions\Http;

/**
 * A HTTP exception with an id and error message that
 * will never change at runtime
 */
abstract class PredefinedHttpException extends BaseHttpException
{
    protected string $id;
    protected int $status;
    protected string $errorMessage;

    public function __construct()
    {
        parent::__construct(
            id: $this->id,
            message: $this->errorMessage,
        );
    }
}
