<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    config([
        'database.connections.passport_migrations_testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ],
        'passport.connection' => 'passport_migrations_testing',
    ]);

    DB::purge('passport_migrations_testing');
});

it('preserves existing Passport tables and their data through migration and rollback', function (): void {
    $schema = Schema::connection('passport_migrations_testing');
    $database = DB::connection('passport_migrations_testing');

    $schema->create('oauth_auth_codes', function (Blueprint $table): void {
        $table->string('id', 100)->primary();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('client_id');
        $table->text('scopes')->nullable();
        $table->boolean('revoked');
        $table->dateTime('expires_at')->nullable();
    });
    $schema->create('oauth_access_tokens', function (Blueprint $table): void {
        $table->string('id', 100)->primary();
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->unsignedBigInteger('client_id');
        $table->string('name', 191)->nullable();
        $table->text('scopes')->nullable();
        $table->boolean('revoked');
        $table->timestamps();
        $table->dateTime('expires_at')->nullable();
    });
    $schema->create('oauth_refresh_tokens', function (Blueprint $table): void {
        $table->string('id', 100)->primary();
        $table->string('access_token_id', 100)->index();
        $table->boolean('revoked');
        $table->dateTime('expires_at')->nullable();
    });
    $schema->create('oauth_clients', function (Blueprint $table): void {
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
    $schema->create('oauth_personal_access_clients', function (Blueprint $table): void {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('client_id');
        $table->timestamps();
    });

    $database->table('oauth_auth_codes')->insert([
        'id' => 'legacy-auth-code',
        'user_id' => 12,
        'client_id' => 7,
        'revoked' => false,
    ]);
    $database->table('oauth_access_tokens')->insert([
        'id' => 'legacy-access-token',
        'user_id' => 12,
        'client_id' => 7,
        'revoked' => false,
    ]);
    $database->table('oauth_refresh_tokens')->insert([
        'id' => 'legacy-refresh-token',
        'access_token_id' => 'legacy-access-token',
        'revoked' => false,
    ]);
    $database->table('oauth_clients')->insert([
        'id' => 7,
        'user_id' => 12,
        'name' => 'Legacy client',
        'redirect' => 'https://client.example.test/callback',
        'personal_access_client' => true,
        'password_client' => false,
        'revoked' => false,
    ]);
    $database->table('oauth_personal_access_clients')->insert([
        'client_id' => 7,
    ]);

    $migrationPaths = glob(database_path('migrations/2026_11_21_03090*_create_oauth_*_table.php'));

    foreach ($migrationPaths as $migrationPath) {
        (require $migrationPath)->up();
    }

    expect($database->table('oauth_auth_codes')->value('id'))->toBe('legacy-auth-code')
        ->and($database->table('oauth_access_tokens')->value('id'))->toBe('legacy-access-token')
        ->and($database->table('oauth_refresh_tokens')->value('id'))->toBe('legacy-refresh-token')
        ->and($database->table('oauth_clients')->value('name'))->toBe('Legacy client')
        ->and($database->table('oauth_personal_access_clients')->value('client_id'))->toBe(7)
        ->and($schema->hasTable('oauth_device_codes'))->toBeTrue();

    foreach (array_reverse($migrationPaths) as $migrationPath) {
        (require $migrationPath)->down();
    }

    expect($schema->hasTable('oauth_auth_codes'))->toBeTrue()
        ->and($schema->hasTable('oauth_access_tokens'))->toBeTrue()
        ->and($schema->hasTable('oauth_refresh_tokens'))->toBeTrue()
        ->and($schema->hasTable('oauth_clients'))->toBeTrue()
        ->and($schema->hasTable('oauth_personal_access_clients'))->toBeTrue()
        ->and($schema->hasTable('oauth_device_codes'))->toBeFalse()
        ->and($schema->hasTable('passport_migration_created_tables'))->toBeFalse();
});

it('rolls back only Passport tables created by these migrations', function (): void {
    $schema = Schema::connection('passport_migrations_testing');
    $migrationPaths = glob(database_path('migrations/2026_11_21_03090*_create_oauth_*_table.php'));

    foreach ($migrationPaths as $migrationPath) {
        (require $migrationPath)->up();
    }

    foreach (array_reverse($migrationPaths) as $migrationPath) {
        (require $migrationPath)->down();
    }

    expect($schema->hasTable('oauth_auth_codes'))->toBeFalse()
        ->and($schema->hasTable('oauth_access_tokens'))->toBeFalse()
        ->and($schema->hasTable('oauth_refresh_tokens'))->toBeFalse()
        ->and($schema->hasTable('oauth_clients'))->toBeFalse()
        ->and($schema->hasTable('oauth_device_codes'))->toBeFalse()
        ->and($schema->hasTable('passport_migration_created_tables'))->toBeFalse();
});

it('preserves pre-existing tables while rolling back newly created Passport tables', function (): void {
    $schema = Schema::connection('passport_migrations_testing');
    $database = DB::connection('passport_migrations_testing');
    $schema->create('oauth_device_codes', function (Blueprint $table): void {
        $table->string('id', 100)->primary();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->unsignedBigInteger('client_id');
        $table->string('user_code', 8)->unique();
        $table->text('scopes');
        $table->boolean('revoked');
        $table->dateTime('user_approved_at')->nullable();
        $table->dateTime('last_polled_at')->nullable();
        $table->dateTime('expires_at')->nullable();
    });
    $database->table('oauth_device_codes')->insert([
        'id' => 'legacy-device-code',
        'client_id' => 7,
        'user_code' => 'old-code',
        'scopes' => '[]',
        'revoked' => false,
    ]);

    $migrationPaths = glob(database_path('migrations/2026_11_21_03090*_create_oauth_*_table.php'));

    foreach ($migrationPaths as $migrationPath) {
        (require $migrationPath)->up();
    }

    foreach (array_reverse($migrationPaths) as $migrationPath) {
        (require $migrationPath)->down();
    }

    expect($database->table('oauth_device_codes')->value('id'))->toBe('legacy-device-code')
        ->and($schema->hasTable('oauth_device_codes'))->toBeTrue()
        ->and($schema->hasTable('oauth_auth_codes'))->toBeFalse()
        ->and($schema->hasTable('oauth_access_tokens'))->toBeFalse()
        ->and($schema->hasTable('oauth_refresh_tokens'))->toBeFalse()
        ->and($schema->hasTable('oauth_clients'))->toBeFalse()
        ->and($schema->hasTable('passport_migration_created_tables'))->toBeFalse();
});
