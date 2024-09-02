<?php

namespace App\Domains\Registration\UseCases;

use App\Core\Data\Exceptions\BadRequestException;
use App\Domains\Registration\Exceptions\AccountAlreadyActivatedException;
use App\Models\Account;
use Repositories\AccountRepository;

final class ActivateUnverifiedAccount
{
    public function execute(string $email)
    {
        if (empty($email)) {
            throw new BadRequestException(id: 'missing_email', message: 'Cannot activate account with no email');
        }

        $account = Account::whereEmail($email)->firstOrFail();

        if ($account->activated === true) {
            throw new AccountAlreadyActivatedException();
        }

        $account->activated = true;
        $account->save();
    }
}
