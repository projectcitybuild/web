<?php

namespace App\Library\APIClient;

use App\Enum;

final class APIRequestMethod extends Enum
{
    const POST = 'post';
    const GET = 'get';
    const PATCH = 'patch';
    const PUT = 'put';
    const DELETE = 'delete';
}