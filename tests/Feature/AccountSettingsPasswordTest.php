<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AccountSettingsPasswordTest extends TestCase
{
    use WithFaker;

    private $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
    }

    private function submitPasswordChange($old, $new, $newConfirmation)
    {
        return $this->actingAs($this->account)->post(route('front.account.settings.password'), [
            'old_password' => $old,
            'new_password' => $new,
            'new_password_confirm' => $newConfirmation,
        ]);
    }

    public function test_password_change()
    {
        $newPassword = $this->faker->password(8);

        $this->submitPasswordChange('secret', $newPassword, $newPassword)
            ->assertSessionHasNoErrors();

        $this->assertTrue(Auth::attempt([
            'email' => $this->account->email,
            'password' => $newPassword,
        ]));
    }

    public function test_incorrect_current_password()
    {
        $this->submitPasswordChange('wrong', 'password', 'password')
            ->assertSessionHasErrors();
    }

    public function test_mismatching_password_confirmation()
    {
        $this->submitPasswordChange('secret', 'password', 'different')
            ->assertSessionHasErrors();
    }

    public function test_empty_current_password()
    {
        $this->submitPasswordChange('', 'new', 'new')
            ->assertSessionHasErrors();
    }

    public function test_empty_new_password()
    {
        $this->submitPasswordChange('secret', '', '')
            ->assertSessionHasErrors();
    }
}
