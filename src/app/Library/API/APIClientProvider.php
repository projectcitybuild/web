<?php

namespace App\Library\API;

interface APIClientProvider
{
    function call(APIRequest $request);
}