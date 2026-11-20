<?php

use App\Domains\Permissions\WebManagePermission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

beforeEach(function (): void {
    config([
        'inertia.ssr.enabled' => false,
        'database.connections.passport_clients_testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ],
        'passport.connection' => 'passport_clients_testing',
    ]);

    DB::purge('passport_clients_testing');

    Schema::connection('passport_clients_testing')->create('oauth_clients', function (Blueprint $table): void {
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

    Schema::connection('passport_clients_testing')->create('oauth_access_tokens', function (Blueprint $table): void {
        $table->string('id', 100)->primary();
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->unsignedBigInteger('client_id');
        $table->string('name', 191)->nullable();
        $table->text('scopes')->nullable();
        $table->boolean('revoked')->default(false);
        $table->timestamps();
        $table->dateTime('expires_at')->nullable();
    });

    Schema::connection('passport_clients_testing')->create('oauth_refresh_tokens', function (Blueprint $table): void {
        $table->string('id', 80)->primary();
        $table->string('access_token_id', 80)->index();
        $table->boolean('revoked');
        $table->dateTime('expires_at')->nullable();
    });
});

it('creates authorization clients and only exposes a new secret once', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_VIEW->value,
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);

    $this->actingAs($manager)
        ->post(route('manage.passport-clients.store'), [
            'name' => 'Web Client',
            'grant_type' => 'authorization_code',
            'confidential' => true,
            'redirect_uris' => "https://client.example.test/callback\nhttps://client.example.test/return",
        ])
        ->assertRedirect(route('manage.passport-clients.index'))
        ->assertSessionHas('created_client_secret');

    $client = Client::query()->firstOrFail();
    $clientSecret = $this->app['session.store']->get('created_client_secret');

    expect($clientSecret)->toBeString()
        ->and($client->secret)->not->toBe($clientSecret)
        ->and($client->hasGrantType('authorization_code'))->toBeTrue()
        ->and($client->redirect_uris)->toBe([
            'https://client.example.test/callback',
            'https://client.example.test/return',
        ]);

    $this->get(route('manage.passport-clients.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PassportClients/PassportClientList')
            ->where('created_client_secret', $clientSecret)
            ->has('clients.0', fn (Assert $clientProps) => $clientProps
                ->where('id', $client->getKey())
                ->missing('secret')
                ->etc()
            )
        );

    $this->get(route('manage.passport-clients.edit', $client))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PassportClients/PassportClientEdit')
            ->where('client.name', 'Web Client')
            ->missing('client.secret')
        );

    $this->put(route('manage.passport-clients.update', $client), [
        'name' => 'Updated Web Client',
        'redirect_uris' => 'https://updated-client.example.test/callback',
    ])->assertRedirect(route('manage.passport-clients.index'));

    expect($client->fresh()->name)->toBe('Updated Web Client')
        ->and($client->fresh()->redirect_uris)->toBe(['https://updated-client.example.test/callback']);

    $this->get(route('manage.passport-clients.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('created_client_secret', null)
        );
});

it('creates public authorization clients without a client secret', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);

    $this->actingAs($manager)
        ->post(route('manage.passport-clients.store'), [
            'name' => 'Public Web Client',
            'grant_type' => 'authorization_code',
            'confidential' => false,
            'redirect_uris' => 'https://public-client.example.test/callback',
        ])
        ->assertRedirect(route('manage.passport-clients.index'));

    $client = Client::query()->firstOrFail();

    expect($client->secret)->toBeNull()
        ->and($client->hasGrantType('authorization_code'))->toBeTrue()
        ->and($client->redirect_uris)->toBe(['https://public-client.example.test/callback']);
});

it('regenerates a confidential client secret and only reveals it once', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);
    $client = app(ClientRepository::class)->createClientCredentialsGrantClient('Secret Rotation Client');
    $previousSecretHash = $client->secret;

    $this->actingAs($manager)
        ->post(route('manage.passport-clients.regenerate-secret', $client))
        ->assertRedirect(route('manage.passport-clients.edit', $client))
        ->assertSessionHas('regenerated_client_secret');

    $newSecret = $this->app['session.store']->get('regenerated_client_secret');
    $newSecretHash = $client->fresh()->secret;

    expect($newSecret)->toBeString()
        ->and($newSecretHash)->not->toBe($previousSecretHash)
        ->and(Hash::check($newSecret, $newSecretHash))->toBeTrue();

    $this->get(route('manage.passport-clients.edit', $client))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PassportClients/PassportClientEdit')
            ->where('regenerated_client_secret', $newSecret)
            ->missing('client.secret')
        );

    $this->get(route('manage.passport-clients.edit', $client))
        ->assertInertia(fn (Assert $page) => $page
            ->where('regenerated_client_secret', null)
        );
});

it('does not regenerate a secret for public clients', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);
    $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
        'Public Client',
        ['https://public-client.example.test/callback'],
        false,
    );

    $this->actingAs($manager)
        ->post(route('manage.passport-clients.regenerate-secret', $client))
        ->assertNotFound();

    expect($client->fresh()->secret)->toBeNull();

    $client->delete();
});

it('creates service clients, ignores grant changes during edits, and revokes their tokens on deletion', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_VIEW->value,
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);

    $this->actingAs($manager)
        ->post(route('manage.passport-clients.store'), [
            'name' => 'Automation Service',
            'grant_type' => 'client_credentials',
        ])
        ->assertRedirect(route('manage.passport-clients.index'));

    $client = Client::query()->firstOrFail();

    expect($client->hasGrantType('client_credentials'))->toBeTrue()
        ->and($client->redirect_uris)->toBe([]);

    $accessToken = $client->tokens()->create([
        'id' => Str::uuid()->toString(),
        'user_id' => null,
        'name' => 'automation',
        'scopes' => [],
        'revoked' => false,
        'expires_at' => now()->addHour(),
    ]);
    $refreshToken = $accessToken->refreshToken()->create([
        'id' => Str::uuid()->toString(),
        'revoked' => false,
        'expires_at' => now()->addDay(),
    ]);

    $this->put(route('manage.passport-clients.update', $client), [
        'name' => 'Updated Automation Service',
        'grant_type' => 'authorization_code',
        'confidential' => false,
        'redirect_uris' => 'https://ignored.example.test/callback',
    ])->assertRedirect(route('manage.passport-clients.index'));

    expect($client->fresh()->name)->toBe('Updated Automation Service')
        ->and($client->fresh()->hasGrantType('client_credentials'))->toBeTrue()
        ->and($client->fresh()->redirect_uris)->toBe([]);

    $this->delete(route('manage.passport-clients.destroy', $client))
        ->assertRedirect(route('manage.passport-clients.index'));

    expect($client->fresh()->revoked)->toBeTrue()
        ->and($accessToken->fresh()->revoked)->toBeTrue()
        ->and($refreshToken->fresh()->revoked)->toBeTrue();
});

it('validates redirect URLs and denies client edits to view-only managers', function () {
    $viewOnlyManager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_VIEW->value,
    ]);

    $this->actingAs($viewOnlyManager)
        ->get(route('manage.passport-clients.index'))
        ->assertOk();

    $this->get(route('manage.passport-clients.create'))
        ->assertForbidden();

    $client = app(ClientRepository::class)->createClientCredentialsGrantClient('View Only Secret Client');

    $this->post(route('manage.passport-clients.regenerate-secret', $client))
        ->assertForbidden();

    $client->delete();

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);

    $this->actingAs($manager)
        ->from(route('manage.passport-clients.create'))
        ->post(route('manage.passport-clients.store'), [
            'name' => 'Invalid Client',
            'grant_type' => 'authorization_code',
            'confidential' => true,
            'redirect_uris' => 'not a URL',
        ])
        ->assertSessionHasErrors(['redirect_uris.0']);

    expect(Client::query()->count())->toBe(0);
});
