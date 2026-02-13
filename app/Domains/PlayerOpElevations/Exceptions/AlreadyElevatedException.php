<?php

namespace App\Domains\PlayerOpElevations\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AlreadyElevatedException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'already_elevated',
                'message' => $this->getMessage() ?: 'You are already OP elevated',
            ],
            Response::HTTP_CONFLICT,
        );
    }
}
