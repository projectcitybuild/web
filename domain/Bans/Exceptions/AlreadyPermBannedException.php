<?php

namespace Domain\Bans\Exceptions;

use App\Exceptions\Http\PredefinedHttpException;

final class AlreadyPermBannedException extends PredefinedHttpException
{
    protected string $id = 'player_already_banned';
    protected string $errorMessage = 'Player is already permanently banned';
    protected int $status = 400;
}
