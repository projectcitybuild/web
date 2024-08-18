<?php

namespace App\Http\Controllers;

use App\Core\Exceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

abstract class APIController
{
    /**
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
