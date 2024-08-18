<?php

namespace App\Http\Controllers;

use App\Core\Data\Exceptions\BadRequestException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class APIController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @throws BadRequestException
     */
    protected function validateRequest(array $requestData, array $rules, array $messages = [])
    {
        $validator = Validator::make($requestData, $rules, $messages);

        if ($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }
    }
}
