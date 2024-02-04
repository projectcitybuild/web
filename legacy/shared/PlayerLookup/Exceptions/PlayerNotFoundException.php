<?php

namespace Shared\PlayerLookup\Exceptions;

use App\Exceptions\Http\PredefinedHttpException;

final class PlayerNotFoundException extends PredefinedHttpException
{
    protected string $id = 'player_not_found';
    protected string $errorMessage = 'Cannot find this player';
    protected int $status = 404;
}
