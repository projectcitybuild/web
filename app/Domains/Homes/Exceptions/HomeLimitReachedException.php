<?php

namespace App\Domains\Homes\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HomeLimitReachedException extends Exception implements ShouldntReport
{
    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'home_limit_reached',
                'message' => $this->getMessage(),
            ],
            Response::HTTP_BAD_REQUEST,
        );
    }
}
