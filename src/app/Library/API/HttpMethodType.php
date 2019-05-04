<?php

namespace App\Library\API;

use App\Enum;

final class HttpMethodType extends Enum
{
    public const GET = 'get';
    public const POST = 'post';
    public const PATCH = 'patch';
    public const PUT = 'put';
    public const DELETE = 'delete';
}