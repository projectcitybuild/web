<?php

namespace App\Domains\PlayerOpElevations\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class NotElevatedException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'not_elevated',
                'message' => $this->getMessage() ?: 'You are not currently OP elevated',
            ],
            Response::HTTP_NOT_FOUND,
        );
    }
}
