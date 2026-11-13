<?php

use Admin9\OidcServer\Services\ClaimsService;
use Admin9\OidcServer\Services\IdTokenService;
use Admin9\OidcServer\Services\PassportKeys;
use App\Models\Account;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

it('publishes discovery using the canonical issuer and supported OIDC flow', function () {
    config(['oidc-server.issuer' => 'https://pcb.example']);

    $this->getJson('/.well-known/openid-configuration')
        ->assertOk()
        ->assertJsonPath('issuer', 'https://pcb.example')
        ->assertJsonPath('authorization_endpoint', 'https://pcb.example/oauth/authorize')
        ->assertJsonPath('response_types_supported', ['code'])
        ->assertJsonPath('code_challenge_methods_supported', ['S256'])
        ->assertJsonPath('id_token_signing_alg_values_supported', ['RS256'])
        ->assertJsonPath('scopes_supported', ['openid', 'profile', 'email']);
});

it('publishes the Passport public key with an RS256 key identifier', function () {
    $key = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    expect($key)->not->toBeFalse();

    openssl_pkey_export($key, $privateKey);
    $keyDetails = openssl_pkey_get_details($key);
    config([
        'passport.private_key' => $privateKey,
        'passport.public_key' => $keyDetails['key'],
    ]);

    $this->getJson('/.well-known/jwks.json')
        ->assertOk()
        ->assertJsonPath('keys.0.kty', 'RSA')
        ->assertJsonPath('keys.0.alg', 'RS256')
        ->assertJsonPath('keys.0.use', 'sig')
        ->assertJsonPath('keys.0.e', 'AQAB')
        ->assertJsonStructure(['keys' => [['kid', 'n', 'e']]]);
});

it('signs ID tokens with the key identifier published by JWKS', function () {
    $key = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    expect($key)->not->toBeFalse();

    openssl_pkey_export($key, $privateKey);
    $keyDetails = openssl_pkey_get_details($key);
    config([
        'passport.private_key' => $privateKey,
        'passport.public_key' => $keyDetails['key'],
        'oidc-server.issuer' => 'https://pcb.example',
        'oidc-server.scopes' => ['openid' => ['claims' => ['sub']]],
    ]);

    $account = new Account;
    $account->setAttribute($account->getAuthIdentifierName(), 1234);
    $scope = Mockery::mock(ScopeEntityInterface::class);
    $scope->shouldReceive('getIdentifier')->andReturn('openid');
    $accessToken = Mockery::mock(AccessTokenEntityInterface::class);
    $accessToken->shouldReceive('getExpiryDateTime')->andReturn(new DateTimeImmutable('+30 minutes'));
    $accessToken->shouldReceive('getScopes')->andReturn([$scope]);
    $client = Mockery::mock(ClientEntityInterface::class);
    $client->shouldReceive('getIdentifier')->andReturn('bookstack-client');

    $jwt = app(IdTokenService::class)->generateToken(
        $accessToken,
        $account,
        $client,
        'nonce-value',
        now()->timestamp,
    );
    $publicKey = app(PassportKeys::class)->key('public')->contents();
    $token = (new Parser(new JoseEncoder))->parse($jwt);
    $userInfoClaims = app(ClaimsService::class)->resolveForUser($account, ['openid']);
    $jwksKey = $this->getJson('/.well-known/jwks.json')->assertOk()->json('keys.0');

    expect($token->headers()->get('alg'))->toBe('RS256')
        ->and($token->headers()->get('kid'))->toBe($jwksKey['kid'])
        ->and($token->claims()->get('sub'))->toBe('1234')
        ->and($token->claims()->get('sub'))->toBe($userInfoClaims['sub'])
        ->and(app(Validator::class)->validate($token, new SignedWith(new Sha256, InMemory::plainText($publicKey))))
        ->toBeTrue();
});

it('requires a valid Passport bearer token for UserInfo', function () {
    $this->getJson('/oauth/userinfo')
        ->assertUnauthorized();
});

it('requires an S256 challenge on every authorization code request', function () {
    config([
        'session.driver' => 'redis',
        'session.connection' => 'default',
        'cache.default' => 'redis',
    ]);

    $this->get('/oauth/authorize?response_type=code')
        ->assertBadRequest()
        ->assertJsonPath('error', 'invalid_request');

    $this->get('/oauth/authorize?response_type=code&code_challenge=challenge&code_challenge_method=plain')
        ->assertBadRequest()
        ->assertJsonPath('error', 'invalid_request');
});

it('preserves the legacy OAuth profile schema', function () {
    $account = Account::factory()->create([
        'id' => 1234,
        'email' => 'player@example.com',
        'username' => 'ExamplePlayer',
    ]);

    $this->actingAs($account, 'api')
        ->getJson('/oauth/me')
        ->assertExactJson([
            'id' => 1234,
            'email' => 'player@example.com',
            'nickname' => 'ExamplePlayer',
            'name' => 'ExamplePlayer',
            'avatar' => null,
        ]);
});
