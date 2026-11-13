<?php

namespace Tests;

use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use App\Models\Role;
use App\Models\ServerToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\Support\TemporaryConfig;
use Tests\Support\TestResponseMacros;

abstract class TestCase extends BaseTestCase
{
    private static ?array $passportTestKeys = null;

    use CreatesApplication;
    use RefreshDatabase;
    use TemporaryConfig;
    use TestResponseMacros;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurePassportTestKeys();

        $this->registerTestResponseMacros();

        // Throw an exception if a HTTP response hasn't been mocked
        Http::preventStrayRequests();
    }

    private function configurePassportTestKeys(): void
    {
        if (self::$passportTestKeys === null) {
            $key = openssl_pkey_new([
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ]);

            if ($key === false || ! openssl_pkey_export($key, $privateKey)) {
                throw new \RuntimeException('Unable to generate Passport test keys.');
            }

            $details = openssl_pkey_get_details($key);
            if ($details === false) {
                throw new \RuntimeException('Unable to read Passport test key details.');
            }

            self::$passportTestKeys = [
                'private' => $privateKey,
                'public' => $details['key'],
            ];
        }

        config([
            'passport.private_key' => self::$passportTestKeys['private'],
            'passport.public_key' => self::$passportTestKeys['public'],
        ]);
    }

    /** @deprecated Do this on a per-test basis */
    protected function disableReauthMiddleware(): TestCase
    {
        Session::put('auth.password_confirmed_at', time());

        return $this;
    }

    /** @deprecated Do this on a per-test basis */
    protected function flagNeedsMfa(): TestCase
    {
        Session::put(MfaAuthenticated::NEEDS_MFA_KEY, 'true');

        return $this;
    }

    protected function withServerToken(?ServerToken $serverToken = null): TestCase
    {
        $serverToken ??= ServerToken::factory()->create();

        return $this->withBearerToken($serverToken->token);
    }

    protected function withBearerToken(string $token): TestCase
    {
        return $this->withHeaders(['Authorization' => 'Bearer '.$token]);
    }

    protected function adminAccount(): Account
    {
        $account = Account::factory()
            ->hasFinishedTotp()
            ->create();

        $role = Role::factory()->administrator()->create();
        $account->roles()->attach($role->id);

        return $account;
    }

    protected function staffAccount(): Account
    {
        $account = Account::factory()
            ->hasFinishedTotp()
            ->create();

        $role = Role::factory()->staff()->create();
        $account->roles()->attach($role->id);

        return $account;
    }
}
