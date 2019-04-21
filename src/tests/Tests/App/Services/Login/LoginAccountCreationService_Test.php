<?php
namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Repositories\AccountLinkRepository;
use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountLink;
use App\Library\OAuth\Entities\OAuthUser;
use App\Services\Login\LoginAccountCreationService;
use App\Services\Login\Exceptions\SocialEmailInUseException;
use Illuminate\Database\Connection;
use Illuminate\Log\Logger;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginAccountCreationService_Test extends TestCase
{
    use RefreshDatabase;

    private $loggerStub;
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggerStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = resolve(Connection::class);
    }

    public function testAccountAndAccountLink_succeeds()
    {
        // given...
        $providerAccount = new OAuthUser('facebook', 'test@pcbmc.co', 'test_user', '123456');
        $account = factory(Account::class)->create();
        AccountLink::create([
            'account_id' => $account->getKey(),
            'provider_name' => $providerAccount->getProviderName(),
            'provider_email' => $providerAccount->getEmail(),
            'provider_id' => $providerAccount->getId(),
        ]);

        $service = new LoginAccountCreationService(new AccountRepository,
                                                   new AccountLinkRepository,
                                                   $this->loggerStub,
                                                   $this->connection);
        // when...
        $hasAccountLink = $service->hasAccountLink($providerAccount);

        // expect...
        $this->assertTrue($hasAccountLink);
    }

    public function testNoAccountLink_fails()
    {
        // given...
        $providerAccount = new OAuthUser('facebook', 'test@pcbmc.co', 'test_user', '123456');

        $service = new LoginAccountCreationService(new AccountRepository,
                                                   new AccountLinkRepository,
                                                   $this->loggerStub,
                                                   $this->connection);
        // when...
        $hasAccountLink = $service->hasAccountLink($providerAccount);

        // expect...
        $this->assertFalse($hasAccountLink);
    }

    public function testCanCreateAccountWithLink()
    {
        // given...
        $email = 'test@pcbmc.co';
        $id = '123456';
        $providerName = 'test_provider';

        $service = new LoginAccountCreationService(new AccountRepository,
                                                   new AccountLinkRepository,
                                                   $this->loggerStub,
                                                   $this->connection);
        // when...
        $service->createAccountWithLink($email, $id, $providerName);

        // expect...
        $this->assertDatabaseHas('accounts', [
            'email' => $email,
        ]);
        $this->assertDatabaseHas('account_links', [
            'provider_name' => $providerName,
            'provider_email' => $email,
            'provider_id' => $id,
        ]);
    }
}
