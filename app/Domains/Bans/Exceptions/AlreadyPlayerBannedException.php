<?php

namespace App\Domains\Bans\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AlreadyPlayerBannedException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response
    {
        return response()->json([
            'id' => 'player_already_banned',
            'message' => $this->getMessage() ?: 'Player is already banned',
        ], Response::HTTP_BAD_REQUEST);
    }
}
