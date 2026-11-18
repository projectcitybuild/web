<?php

namespace App\Domains\Bans\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class BanNotFoundException extends Exception implements ShouldntReport
{
    public function render(Request $request): Response
    {
        return response()->json(
            [
                'id' => 'ban_not_found',
                'message' => $this->getMessage() ?: 'Existing ban not found',
            ],
            Response::HTTP_BAD_REQUEST,
        );
    }
}
