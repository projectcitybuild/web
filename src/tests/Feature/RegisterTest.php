<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\UnactivatedAccount;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testUserCanRegister()
    {
        $unactivatedAccount = factory(UnactivatedAccount::class)->states('unhashed', 'with-confirm')->make();

        $this->post(route('front.register.submit'), $unactivatedAccount->toArray())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('accounts_unactivated', $unactivatedAccount->only('email', 'username'));
    }

    public function testUserCannotRegisterWithSameEmailAsActivatedAccount()
    {
        $existingAccount = factory(Account::class)->create();

        $newAccount = factory(UnactivatedAccount::class)->states('unhashed', 'with-confirm')->make([
            'email' => $existingAccount->email
        ]);

        $this->post(route('front.register.submit'), $newAccount->toArray())
            ->assertSessionHasErrors();
    }

    public function testUserCannotRegisterWithSameEmailAsUnactivatedAccount()
    {
        $existingUnactivatedAccount = factory(UnactivatedAccount::class)->create();

        $newAccount = factory(UnactivatedAccount::class)->states('unhashed', 'with-confirm')->make([
            'email' => $existingUnactivatedAccount->email
        ]);

        $this->post(route('front.register.submit'), $newAccount->toArray())
            ->assertSessionHasErrors();
    }

    public function testUserCannotRegisterWithSameUsernameAsUnactivatedAccount()
    {
        $existingUnactivatedAccount = factory(UnactivatedAccount::class)->create();

        $newAccount = factory(UnactivatedAccount::class)->states('unhashed', 'with-confirm')->make([
            'username' => $existingUnactivatedAccount->username
        ]);

        $this->post(route('front.register.submit'), $newAccount->toArray())
            ->assertSessionHasErrors();
    }
}
