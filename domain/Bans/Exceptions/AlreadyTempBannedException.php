<?php

namespace Domain\Bans\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

final class AlreadyTempBannedException extends PredefinedHttpException
{
    protected string $id = 'player_already_temp_banned';
    protected string $errorMessage = 'Player is already banned temporarily';
    protected int $status = 400;
}
