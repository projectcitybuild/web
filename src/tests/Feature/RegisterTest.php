<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testUserCanRegister()
    {
        $unactivatedAccount = factory(Account::class)->states('unhashed', 'with-confirm', 'unactivated')->make();

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

    public function testAssertPasswordIsHashed()
    {
        $unactivatedAccount = factory(Account::class)->states('unhashed', 'with-confirm')->make();

        $this->post(route('front.register.submit'), $unactivatedAccount->toArray())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('accounts', [
            'email' => $unactivatedAccount->email,
            'password' => $unactivatedAccount->password
        ]);
    }

    public function testNewMemberIsPutInDefaultGroup()
    {
        $memberGroup = Group::create([
            'name' => 'member',
            'is_default' => 1
        ]);

        $unactivatedAccount = factory(Account::class)->states('unhashed', 'with-confirm', 'unactivated')->make();

        $this->post(route('front.register.submit'), $unactivatedAccount->toArray())
            ->assertSessionHasNoErrors();

        $account = Account::where('email', $unactivatedAccount["email"])->firstOrFail();

        $this->assertDatabaseHas('groups_accounts', [
            'group_id' => $memberGroup->group_id,
            'account_id' => $account->account_id
        ]);
    }
}
