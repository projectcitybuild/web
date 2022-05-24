<?php

namespace Domain\BanAppeals\Exceptions;

use Nette\Schema\ValidationException;

class EmailRequiredException extends \Exception
{
    public function throwAsValidationException()
    {
        throw \Illuminate\Validation\ValidationException::withMessages(
            ['email' => __('validation.required', ['attribute' => 'email'])]
        );
}
}
