<?php

namespace App\Domains\Bans\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AlreadyBannedException extends Exception implements ShouldntReport
{
    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'already_banned',
                'message' => $this->getMessage() ?: 'Duplicate entry (already banned)',
            ],
            Response::HTTP_CONFLICT,
        );
    }
}
