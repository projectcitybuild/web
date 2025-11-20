<?php

namespace App\Domains\Bans\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class NotIPBannedException extends Exception
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response
    {
        return response()->json([
            'id' => 'ip_not_banned',
            'message' => $this->getMessage() ?: 'IP address is not currently banned',
        ], Response::HTTP_BAD_REQUEST);
    }
}
