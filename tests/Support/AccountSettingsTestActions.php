<?php

namespace Tests\Support;

use App\Models\Account;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/** @mixin TestCase */
trait AccountSettingsTestActions
{
    protected function submitEmailChange(Account $account, string $newEmail): TestResponse
    {
        return $this->actingAs($account)->post(
            uri: route('front.account.settings.email'),
            data: ['email' => $newEmail],
        );
    }

    protected function submitPasswordChange(
        Account $account,
        string $oldPassword,
        string $newPassword,
        string $newPasswordConfirmation,
    ): TestResponse {
        return $this->actingAs($account)->post(route('front.account.settings.password'), [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password_confirm' => $newPasswordConfirmation,
        ]);
    }

    protected function submitUsernameChange(Account $account, string $newUsername): TestResponse
    {
        return $this->actingAs($account)->post(route('front.account.settings.username'), [
            'username' => $newUsername,
        ]);
    }
}
