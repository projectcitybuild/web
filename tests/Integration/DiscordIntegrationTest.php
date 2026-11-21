<?php

use App\Core\Domains\Discord\Jobs\SyncDiscordRoleJob;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Middleware\OidcSharedState;
use App\Models\Account;
use App\Models\DiscordRoleMapping;
use App\Models\ExternalAccount;
use App\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\Support\StaffAccountFactory;
use Tests\TestCase;

uses(StaffAccountFactory::class);

beforeEach(function () {
    config([
        'database.connections.discord_passport_testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ],
        'passport.connection' => 'discord_passport_testing',
    ]);

    DB::purge('discord_passport_testing');

    Schema::connection('discord_passport_testing')->create('oauth_clients', function (Blueprint $table): void {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->string('name', 191);
        $table->string('secret', 100)->nullable();
        $table->string('provider', 191)->nullable();
        $table->text('redirect');
        $table->boolean('personal_access_client')->default(false);
        $table->boolean('password_client')->default(false);
        $table->boolean('revoked')->default(false);
        $table->timestamps();
    });

    Schema::connection('discord_passport_testing')->create('oauth_access_tokens', function (Blueprint $table): void {
        $table->string('id', 100)->primary();
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->unsignedBigInteger('client_id');
        $table->string('name', 191)->nullable();
        $table->text('scopes')->nullable();
        $table->boolean('revoked')->default(false);
        $table->timestamps();
        $table->dateTime('expires_at')->nullable();
    });

    Passport::clientCredentialsTokensExpireIn(now()->addMinutes(30));
});

function createDiscordIntegrationClient(string $name): Client
{
    return app(ClientRepository::class)->createClientCredentialsGrantClient($name);
}

function issueDiscordIntegrationToken(TestCase $test, Client $client, string $scope = 'discord.roles.sync'): string
{
    $test->withoutMiddleware(OidcSharedState::class);

    $response = $test->postJson('/oauth/token', [
        'grant_type' => 'client_credentials',
        'client_id' => $client->getKey(),
        'client_secret' => $client->plainSecret,
        'scope' => $scope,
    ])->assertOk();

    $token = $response->json('access_token');

    if (! is_string($token)) {
        throw new RuntimeException('Passport did not issue an access token.');
    }

    return $token;
}

function discordLinkCallbackUrl(TestCase $test, Account $account, string $discordUserId, string $username): string
{
    $startResponse = $test->actingAs($account)
        ->get(route('front.account.settings.discord.connect'));
    $location = $startResponse->headers->get('Location');
    parse_str((string) parse_url((string) $location, PHP_URL_QUERY), $authorization);

    Http::fake([
        'https://discord.com/api/oauth2/token' => Http::response(['access_token' => 'oauth-access-token']),
        'https://discord.com/api/users/@me' => Http::response([
            'id' => $discordUserId,
            'username' => $username,
        ]),
    ]);

    return route('front.account.settings.discord.callback', [
        'code' => 'authorization-code',
        'state' => $authorization['state'] ?? '',
    ]);
}

it('links a Discord account only after validating the OAuth state and remote identity', function () {
    config([
        'audit.console' => true,
        'services.discord.client_id' => 'discord-client-id',
        'services.discord.client_secret' => 'discord-client-secret',
        'services.discord.redirect_uri' => 'https://citybuild.example/account/settings/discord/callback',
    ]);
    Bus::fake([SyncDiscordRoleJob::class]);
    $account = Account::factory()->create();
    $discordUserId = '123456789012345678';

    $callbackUrl = discordLinkCallbackUrl($this, $account, $discordUserId, 'builder');
    $response = $this->get($callbackUrl);

    $response->assertRedirect(route('front.account.settings.discord'))
        ->assertSessionHas('success', 'Your Discord account has been linked.');
    $this->assertDatabaseHas('external_accounts', [
        'account_id' => $account->id,
        'provider' => ExternalAccount::PROVIDER_DISCORD,
        'provider_user_id' => $discordUserId,
        'username' => 'builder',
    ]);
    $audit = $account->audits()->where('event', 'connected Discord account')->firstOrFail();
    expect($audit->new_values)->toBe([
        'discord_user_id' => $discordUserId,
        'discord_username' => 'builder',
    ]);
    Bus::assertDispatched(SyncDiscordRoleJob::class, fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $discordUserId
        && $job->accountEvent === 'linked'
    );
});

it('rejects an OAuth state mismatch without contacting Discord', function () {
    config([
        'services.discord.client_id' => 'discord-client-id',
        'services.discord.client_secret' => 'discord-client-secret',
        'services.discord.redirect_uri' => 'https://citybuild.example/account/settings/discord/callback',
    ]);
    $account = Account::factory()->create();
    $this->actingAs($account)->get(route('front.account.settings.discord.connect'));

    Http::fake();

    $this->get(route('front.account.settings.discord.callback', [
        'code' => 'authorization-code',
        'state' => 'incorrect-state',
    ]))
        ->assertRedirect(route('front.account.settings.discord'))
        ->assertSessionHas('error');

    Http::assertNothingSent();
    $this->assertDatabaseCount('external_accounts', 0);
});

it('does not allow a Discord identity to be linked to a different account', function () {
    config([
        'services.discord.client_id' => 'discord-client-id',
        'services.discord.client_secret' => 'discord-client-secret',
        'services.discord.redirect_uri' => 'https://citybuild.example/account/settings/discord/callback',
    ]);
    $discordUserId = '123456789012345678';
    ExternalAccount::factory()->create(['provider_user_id' => $discordUserId]);
    $account = Account::factory()->create();

    $response = $this->get(discordLinkCallbackUrl($this, $account, $discordUserId, 'builder'));

    $response->assertRedirect(route('front.account.settings.discord'))
        ->assertSessionHas('error');
    $this->assertDatabaseCount('external_accounts', 1);
});

it('does not allow an account to link a second Discord identity and refreshes changed usernames', function () {
    config([
        'audit.console' => true,
        'services.discord.client_id' => 'discord-client-id',
        'services.discord.client_secret' => 'discord-client-secret',
        'services.discord.redirect_uri' => 'https://citybuild.example/account/settings/discord/callback',
    ]);
    $account = Account::factory()->create();
    $firstDiscordUserId = '123456789012345678';
    $existing = ExternalAccount::factory()->create([
        'account_id' => $account->id,
        'provider_user_id' => $firstDiscordUserId,
        'username' => 'old-name',
    ]);
    Bus::fake([SyncDiscordRoleJob::class]);
    Http::fake([
        'https://discord.com/api/oauth2/token' => Http::sequence()
            ->push(['access_token' => 'first-oauth-access-token'])
            ->push(['access_token' => 'second-oauth-access-token']),
        'https://discord.com/api/users/@me' => Http::sequence()
            ->push(['id' => $firstDiscordUserId, 'username' => 'new-name'])
            ->push(['id' => '234567890123456789', 'username' => 'another-user']),
    ]);

    $this->get(discordLinkCallbackUrl($this, $account, $firstDiscordUserId, 'new-name'))
        ->assertSessionHas('success');

    Bus::assertDispatched(SyncDiscordRoleJob::class, fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $firstDiscordUserId
        && $job->accountEvent === null
    );

    $this->assertDatabaseHas('external_accounts', [
        'id' => $existing->id,
        'provider_user_id' => $firstDiscordUserId,
        'username' => 'new-name',
    ]);
    $audit = $account->audits()->where('event', 'reverified Discord account')->firstOrFail();
    expect($audit->old_values)->toBe(['discord_username' => 'old-name'])
        ->and($audit->new_values)->toBe(['discord_username' => 'new-name']);

    $this->get(discordLinkCallbackUrl($this, $account, '234567890123456789', 'another-user'))
        ->assertSessionHas('error');

    $this->assertDatabaseCount('external_accounts', 1);
});

it('requires a website login to start Discord account linking', function () {
    $this->get(route('front.account.settings.discord.connect'))
        ->assertRedirect(route('front.login'));
});

it('unlinks a Discord identity and queues cleanup for that Discord user', function () {
    config(['audit.console' => true]);
    Bus::fake([SyncDiscordRoleJob::class]);
    $guildId = '987654321098765432';
    config(['services.discord.guild_id' => $guildId]);
    $account = Account::factory()->create();
    $managedRole = Role::factory()->create();
    $disabledRole = Role::factory()->create();
    $account->roles()->attach([$managedRole->id, $disabledRole->id]);
    $externalAccount = ExternalAccount::factory()->create([
        'account_id' => $account->id,
        'provider_user_id' => '123456789012345678',
    ]);
    $managedMapping = DiscordRoleMapping::factory()->create([
        'guild_id' => $guildId,
        'role_id' => $managedRole->id,
        'discord_role_id' => '234567890123456789',
    ]);
    $disabledMapping = DiscordRoleMapping::factory()->create([
        'guild_id' => $guildId,
        'role_id' => $disabledRole->id,
        'discord_role_id' => '345678901234567890',
        'enabled' => false,
    ]);

    $this->actingAs($account)
        ->delete(route('front.account.settings.discord.unlink'))
        ->assertRedirect(route('front.account.settings.discord'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('external_accounts', ['id' => $externalAccount->id]);
    $audit = $account->audits()->where('event', 'disconnected Discord account')->firstOrFail();
    expect($audit->old_values)->toBe([
        'discord_user_id' => $externalAccount->provider_user_id,
        'discord_username' => $externalAccount->username,
    ]);
    Bus::assertDispatched(SyncDiscordRoleJob::class, fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $externalAccount->provider_user_id
        && $job->accountEvent === 'disconnected'
    );

    $client = createDiscordIntegrationClient('CityBuild Discord Integration');
    config(['services.discord.passport_client_id' => (string) $client->getKey()]);
    $token = issueDiscordIntegrationToken($this, $client);

    $this->withToken($token)
        ->getJson('http://api.localhost/v3/discord/users/123456789012345678/roles')
        ->assertExactJson([
            'discord_user_id' => '123456789012345678',
            'guild_id' => $guildId,
            'linked' => false,
            'managed_role_ids' => [
                $managedMapping->discord_role_id,
                $disabledMapping->discord_role_id,
            ],
            'desired_role_ids' => [],
        ]);
});

it('notifies the bot with a signed idempotent callback', function () {
    config([
        'services.discord.bot_callback_url' => 'https://bot.example.test/discord/role-sync',
        'services.discord.callback_secret' => 'callback-secret',
    ]);
    Http::fake([
        'https://bot.example.test/discord/role-sync' => Http::response([], 202),
    ]);
    $job = new SyncDiscordRoleJob('123456789012345678', 'linked');

    $job->handle();

    Http::assertSent(function (Request $request) use ($job): bool {
        $timestamp = (string) $request->header('X-Discord-Timestamp')[0];
        $signature = hash_hmac(
            'sha256',
            $timestamp.'.'.$request->body(),
            'callback-secret',
        );

        return $request->url() === 'https://bot.example.test/discord/role-sync'
            && $request->hasHeader('Idempotency-Key', $job->syncId)
            && $request->hasHeader('X-Discord-Signature', 'sha256='.$signature)
            && $request['discord_user_id'] === $job->discordUserId
            && $request['account_event'] === 'linked';
    });
});

it('reconciles Discord roles again when a change occurs during an active sync', function () {
    config([
        'queue.default' => 'sync',
        'services.discord.bot_callback_url' => 'https://bot.example.test/discord/role-sync',
        'services.discord.callback_secret' => 'callback-secret',
    ]);
    $callbackCount = 0;
    Http::fake([
        'https://bot.example.test/discord/role-sync' => function (Request $request) use (&$callbackCount) {
            $callbackCount++;

            if ($callbackCount === 1) {
                SyncDiscordRoleJob::dispatch($request['discord_user_id']);
            }

            return Http::response([], 202);
        },
    ]);

    SyncDiscordRoleJob::dispatch('123456789012345678');

    Http::assertSentCount(2);
});

it('issues a client credentials token with the Discord role scope and returns managed roles', function () {
    $guildId = '987654321098765432';
    config(['services.discord.guild_id' => $guildId]);
    $account = Account::factory()->create();
    $mappedRole = Role::factory()->create();
    $disabledRole = Role::factory()->create();
    $unmappedRole = Role::factory()->create();
    $account->roles()->attach([$mappedRole->id, $disabledRole->id, $unmappedRole->id]);
    ExternalAccount::factory()->create([
        'account_id' => $account->id,
        'provider_user_id' => '123456789012345678',
    ]);
    $activeMapping = DiscordRoleMapping::factory()->create([
        'guild_id' => $guildId,
        'role_id' => $mappedRole->id,
        'discord_role_id' => '234567890123456789',
    ]);
    $disabledMapping = DiscordRoleMapping::factory()->create([
        'guild_id' => $guildId,
        'role_id' => $disabledRole->id,
        'discord_role_id' => '345678901234567890',
        'enabled' => false,
    ]);
    $client = createDiscordIntegrationClient('CityBuild Discord Integration');
    config(['services.discord.passport_client_id' => (string) $client->getKey()]);
    $token = issueDiscordIntegrationToken($this, $client);
    $tokenRecord = Passport::token()->newQuery()->where('client_id', $client->getKey())->firstOrFail();

    expect(is_numeric($client->getKey()))->toBeTrue()
        ->and($client->hasGrantType('client_credentials'))->toBeTrue()
        ->and($tokenRecord->user_id)->toBeNull();

    $this->withToken($token)
        ->getJson('http://api.localhost/v3/discord/users/123456789012345678/roles')
        ->assertOk()
        ->assertExactJson([
            'discord_user_id' => '123456789012345678',
            'guild_id' => $guildId,
            'linked' => true,
            'managed_role_ids' => [
                $activeMapping->discord_role_id,
                $disabledMapping->discord_role_id,
            ],
            'desired_role_ids' => [$activeMapping->discord_role_id],
        ]);
});

it('rejects requests without a token, with an invalid token, or from a human access token', function () {
    $client = createDiscordIntegrationClient('CityBuild Discord Integration');
    $integrationClientId = (string) $client->getKey();
    config(['services.discord.passport_client_id' => $integrationClientId]);
    $url = 'http://api.localhost/v3/discord/users/123456789012345678/roles';

    $this->getJson($url)->assertUnauthorized();
    $this->withToken('invalid-token')->getJson($url)->assertUnauthorized();

    $account = Account::factory()->create(['id' => $client->getKey()]);
    Passport::actingAs($account, ['discord.roles.sync'], 'api', $client);
    $this->getJson($url)->assertUnauthorized();
});

it('rejects a client credentials token without the required scope or from another client', function () {
    $integrationClient = createDiscordIntegrationClient('CityBuild Discord Integration');
    $otherClient = createDiscordIntegrationClient('Another Integration');
    config(['services.discord.passport_client_id' => (string) $integrationClient->getKey()]);
    $url = 'http://api.localhost/v3/discord/users/123456789012345678/roles';

    $tokenWithoutScope = issueDiscordIntegrationToken($this, $integrationClient, 'openid');
    $this->withToken($tokenWithoutScope)->getJson($url)->assertForbidden();

    $tokenFromOtherClient = issueDiscordIntegrationToken($this, $otherClient);
    $this->withToken($tokenFromOtherClient)->getJson($url)->assertUnauthorized();
});

it('rejects an expired client credentials token', function () {
    $client = createDiscordIntegrationClient('CityBuild Discord Integration');
    config(['services.discord.passport_client_id' => (string) $client->getKey()]);
    Passport::clientCredentialsTokensExpireIn(now()->subMinute());
    $token = issueDiscordIntegrationToken($this, $client);
    $this->withToken($token)
        ->getJson('http://api.localhost/v3/discord/users/123456789012345678/roles')
        ->assertUnauthorized();
});

it('rejects a revoked client credentials token', function () {
    $client = createDiscordIntegrationClient('CityBuild Discord Integration');
    config(['services.discord.passport_client_id' => (string) $client->getKey()]);
    $token = issueDiscordIntegrationToken($this, $client);
    Passport::token()->newQuery()->where('client_id', $client->getKey())->firstOrFail()->revoke();

    $this->withToken($token)
        ->getJson('http://api.localhost/v3/discord/users/123456789012345678/roles')
        ->assertUnauthorized();
});

it('validates role mappings and queues syncs when mappings are changed', function () {
    config([
        'audit.console' => true,
        'services.discord.guild_id' => '987654321098765432',
    ]);
    Bus::fake([SyncDiscordRoleJob::class]);
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::ROLES_VIEW->value,
        WebManagePermission::ROLES_EDIT->value,
    ]);
    $account = Account::factory()->create();
    $externalAccount = ExternalAccount::factory()->create(['account_id' => $account->id]);
    $role = Role::factory()->create();

    $this->actingAs($manager)
        ->post(route('manage.discord-role-mappings.store'), [
            'role_id' => $role->id,
            'discord_role_id' => '234567890123456789',
        ])
        ->assertRedirect(route('manage.discord-role-mappings.index'));

    $mapping = DiscordRoleMapping::query()->where('role_id', $role->id)->firstOrFail();
    $this->assertDatabaseHas('discord_role_mappings', [
        'id' => $mapping->id,
        'guild_id' => '987654321098765432',
        'enabled' => true,
    ]);
    $createdAudit = $mapping->audits()->where('event', 'created')->firstOrFail();
    expect($createdAudit->new_values)->toMatchArray([
        'guild_id' => '987654321098765432',
        'role_id' => $role->id,
        'discord_role_id' => '234567890123456789',
        'enabled' => true,
    ]);

    $this->put(route('manage.discord-role-mappings.update', $mapping), ['enabled' => false])
        ->assertRedirect(route('manage.discord-role-mappings.index'));

    $this->assertDatabaseHas('discord_role_mappings', [
        'id' => $mapping->id,
        'enabled' => false,
    ]);
    $updatedAudit = $mapping->audits()->where('event', 'updated')->firstOrFail();
    expect((bool) $updatedAudit->old_values['enabled'])->toBeTrue()
        ->and((bool) $updatedAudit->new_values['enabled'])->toBeFalse();
    Bus::assertDispatched(SyncDiscordRoleJob::class, fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $externalAccount->provider_user_id);

    $this->post(route('manage.discord-role-mappings.store'), [
        'role_id' => $role->id,
        'discord_role_id' => 'not-a-snowflake',
    ])->assertSessionHasErrors(['role_id', 'discord_role_id']);
});

it('keeps mapped website roles from being deleted', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ROLES_EDIT->value]);
    $role = Role::factory()->create();
    DiscordRoleMapping::factory()->create([
        'role_id' => $role->id,
        'guild_id' => '987654321098765432',
    ]);

    $this->actingAs($manager)
        ->delete('/manage/roles/'.$role->id)
        ->assertRedirect(route('manage.roles.index'))
        ->assertSessionHasErrors('role');

    $this->assertModelExists($role);
});

it('deletes a Discord role mapping and audits the deletion', function () {
    config([
        'audit.console' => true,
        'services.discord.guild_id' => '987654321098765432',
    ]);
    Bus::fake([SyncDiscordRoleJob::class]);
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ROLES_EDIT->value]);
    $account = Account::factory()->create();
    $externalAccount = ExternalAccount::factory()->create(['account_id' => $account->id]);
    $role = Role::factory()->create();
    $mapping = DiscordRoleMapping::factory()->create([
        'guild_id' => '987654321098765432',
        'role_id' => $role->id,
        'discord_role_id' => '234567890123456789',
    ]);

    $this->actingAs($manager)
        ->delete(route('manage.discord-role-mappings.destroy', $mapping))
        ->assertRedirect(route('manage.discord-role-mappings.index'))
        ->assertSessionHas('success', 'Discord role mapping deleted successfully.');

    $this->assertModelMissing($mapping);
    $audit = $mapping->audits()->where('event', 'deleted')->firstOrFail();
    expect($audit->old_values)->toMatchArray([
        'guild_id' => '987654321098765432',
        'role_id' => $role->id,
        'discord_role_id' => '234567890123456789',
    ]);
    Bus::assertDispatched(
        SyncDiscordRoleJob::class,
        fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $externalAccount->provider_user_id,
    );
});

it('forbids deleting a Discord role mapping without edit permission', function () {
    config(['services.discord.guild_id' => '987654321098765432']);
    Bus::fake([SyncDiscordRoleJob::class]);
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ROLES_VIEW->value]);
    $mapping = DiscordRoleMapping::factory()->create(['guild_id' => '987654321098765432']);

    $this->actingAs($manager)
        ->delete(route('manage.discord-role-mappings.destroy', $mapping))
        ->assertForbidden();

    $this->assertModelExists($mapping);
    Bus::assertNotDispatched(SyncDiscordRoleJob::class);
});

it('does not delete a Discord role mapping from another guild', function () {
    config(['services.discord.guild_id' => '987654321098765432']);
    Bus::fake([SyncDiscordRoleJob::class]);
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ROLES_EDIT->value]);
    $mapping = DiscordRoleMapping::factory()->create(['guild_id' => '876543210987654321']);

    $this->actingAs($manager)
        ->delete(route('manage.discord-role-mappings.destroy', $mapping))
        ->assertNotFound();

    $this->assertModelExists($mapping);
    Bus::assertNotDispatched(SyncDiscordRoleJob::class);
});
