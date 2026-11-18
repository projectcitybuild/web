<?php

namespace App\Domains\Pim\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AlreadyElevatedException extends Exception implements ShouldntReport
{
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
