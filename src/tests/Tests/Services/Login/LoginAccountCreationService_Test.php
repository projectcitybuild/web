<?php
namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Entities\Accounts\Repositories\AccountRepository;
use Entities\Accounts\Repositories\AccountLinkRepository;
use Entities\Accounts\Models\Account;
use Entities\Accounts\Models\AccountLink;
use Domains\Library\OAuth\OAuthUser;
use Domains\Services\Login\LoginAccountCreationService;
use Domains\Services\Login\Exceptions\SocialEmailInUseException;
use Illuminate\Database\Connection;
use Illuminate\Log\Logger;

class LoginAccountCreationService_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $loggerStub;
    private $connection;

    public function setUp()
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

    public function testNoAccountLink_butEmailInUse_fails()
    {
        // given...
        $account = factory(Account::class)->create();
        $providerAccount = new OAuthUser('facebook', $account->email, 'test_user', '123456');

        $service = new LoginAccountCreationService(new AccountRepository,
                                                   new AccountLinkRepository,
                                                   $this->loggerStub,
                                                   $this->connection);
        // expect...
        $this->expectException(SocialEmailInUseException::class);

        // when...
        $hasAccountLink = $service->hasAccountLink($providerAccount);
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
