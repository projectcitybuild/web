<?php

namespace App\Domains\Homes\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HomeAlreadyExistsException extends Exception implements ShouldntReport
{
    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'home_already_exists',
                'message' => $this->getMessage() ?: 'You already have a home with this name',
            ],
            Response::HTTP_CONFLICT,
        );
    }
}
