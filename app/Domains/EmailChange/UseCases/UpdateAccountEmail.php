<?php

namespace App\Domains\EmailChange\UseCases;

use App\Domains\EmailChange\Notifications\EmailChangedNotification;
use App\Models\Account;
use App\Models\EmailChange;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

final class UpdateAccountEmail
{
    public function execute(
        Account $account,
        EmailChange $emailChangeRequest,
        string $oldEmail,
    ) {
        $newEmail = $emailChangeRequest->email;

        DB::transaction(function () use ($account, $emailChangeRequest) {
            $account->email = $emailChangeRequest->email;
            $account->save();

            $emailChangeRequest->delete();
        });

        Notification::route(channel: 'mail', route: $oldEmail)->notify(
            new EmailChangedNotification(newEmail: $newEmail),
        );
    }
}
