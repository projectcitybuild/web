<?php

namespace Domain\Bans\Exceptions;

use App\Exceptions\Http\PredefinedHttpException;

final class PlayerNotBannedException extends PredefinedHttpException
{
    protected string $id = 'player_not_banned';
    protected string $errorMessage = 'This player is not currently banned';
    protected int $status = 400;
}
