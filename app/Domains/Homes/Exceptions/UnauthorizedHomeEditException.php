<?php

namespace App\Domains\Homes\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedHomeEditException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'unauthorized_home_edit',
                'message' => $this->getMessage() ?: 'You are not permitted to edit this home',
            ],
            Response::HTTP_FORBIDDEN,
        );
    }
}
