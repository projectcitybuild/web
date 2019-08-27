<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testUserCanRegister()
    {
        $unactivatedAccount = factory(Account::class)->states('unhashed', 'with-confirm')->make();

        $this->post(route('front.register.submit'), $unactivatedAccount->toArray())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('accounts', [
            'email' => $unactivatedAccount->email,
            'username' => $unactivatedAccount->username,
            'activated' => false
        ]);
    }

    public function testUserCannotRegisterWithSameEmailAsOtherAccount()
    {
        $existingAccount = factory(Account::class)->create();

        $newAccount = factory(Account::class)->states('unhashed', 'with-confirm')->make([
            'email' => $existingAccount->email
        ]);

        $this->post(route('front.register.submit'), $newAccount->toArray())
            ->assertSessionHasErrors();
    }

    public function testUserCannotRegisterWithSameUsernameAsOtherAccount()
    {
        $existingAccount = factory(Account::class)->create();

        $newAccount = factory(Account::class)->states('unhashed', 'with-confirm')->make([
            'username' => $existingAccount->username
        ]);

        $this->post(route('front.register.submit'), $newAccount->toArray())
            ->assertSessionHasErrors();
    }
}
