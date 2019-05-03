<?php

namespace App\Http;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Http\BadRequestException;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function validateRequest(array $requestData, array $rules, array $messages = []) 
    {
        $validator = Validator::make($requestData, $rules, $messages);

        if ($validator->fails()) {
            throw new BadRequestException('bad_input', $validator->errors()->first());
        }
    }
}
