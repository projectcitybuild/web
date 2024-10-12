<?php

namespace App\Http\Controllers;

use App\Core\Data\Exceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

abstract class ApiController
{
    /**
     * @deprecated Use Request validate() instead
     * @throws BadRequestException
     */
    protected function validateRequest(array $requestData, array $rules, array $messages = []): void
    {
        $validator = Validator::make($requestData, $rules, $messages);

        if ($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }
    }
}
