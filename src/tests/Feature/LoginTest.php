<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private $account;

   protected function setUp(): void
   {
       parent::setUp();
       $this->account = factory(Account::class)->create();
   }

    public function testRedirectBackToIntendedPage()
    {
        $this->get(route('front.account.settings'))
            ->assertRedirect(route('front.login'));

        $this->withoutExceptionHandling();

        $this->post(route('front.login.submit'), [
                'email' => $this->account->email,
                'password' => "secret"
            ])
           ->assertRedirect(route('front.account.settings'));
    }
}
