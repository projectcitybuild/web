<?php

namespace App\Domains\Pim\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class NotElevatedException extends Exception implements ShouldntReport
{
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
