<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Library\Discourse\Api\DiscourseAdminApi;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = factory(Account::class)->create();
    }

    public function testUserIsRedirectedToDiscourseSSOWhenLoggingInWithCorrectCredentials()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => "secret"
        ])
            ->assertRedirect("/sso/discourse");
    }

    public function testUserIsShownErrorWithWrongCredentials()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => "wrong"
        ]);

        $this->assertFalse(Auth::check());
    }

    public function testUserWithNullUsernameFetchesFromDiscourse()
    {
        $user = factory(Account::class)->create(["username" => null]);

        $this->mock(DiscourseAdminApi::class, function ($mock) use ($user) {
            $mock->shouldReceive('fetchUserByEmail')->with($user->email)->once();
        });

        $this->post(route('front.login.submit'), [
            'email' => $user->email,
            'password' => "secret"
        ]);
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
