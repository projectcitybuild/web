<?php

namespace Domain\BanAppeals\Exceptions;

use Illuminate\Validation\ValidationException;

class EmailRequiredException extends \Exception
{
    public function throwAsValidationException()
    {
        throw ValidationException::withMessages(
            ['email' => __('validation.required', ['attribute' => 'email'])]
        );
    }
}
