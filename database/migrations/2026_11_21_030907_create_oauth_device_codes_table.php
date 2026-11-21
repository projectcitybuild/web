<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());

        if ($schema->hasTable('oauth_device_codes')) {
            return;
        }

        $schema->create('oauth_device_codes', function (Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('client_id')->index();
            $table->char('user_code', 8)->unique();
            $table->text('scopes');
            $table->boolean('revoked');
            $table->dateTime('user_approved_at')->nullable();
            $table->dateTime('last_polled_at')->nullable();
            $table->dateTime('expires_at')->nullable();
        });

        $this->recordCreatedTable();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());

        if (! $schema->hasTable('passport_migration_created_tables')) {
            return;
        }

        $createdTables = DB::connection($this->getConnection())->table('passport_migration_created_tables');

        if (! $createdTables->where('migration', '2026_11_21_030907_create_oauth_device_codes_table')->exists()) {
            if (! DB::connection($this->getConnection())->table('passport_migration_created_tables')->exists()) {
                $schema->drop('passport_migration_created_tables');
            }

            return;
        }

        $schema->dropIfExists('oauth_device_codes');
        DB::connection($this->getConnection())->table('passport_migration_created_tables')
            ->where('migration', '2026_11_21_030907_create_oauth_device_codes_table')
            ->delete();

        if (! DB::connection($this->getConnection())->table('passport_migration_created_tables')->exists()) {
            $schema->drop('passport_migration_created_tables');
        }
    }

    private function recordCreatedTable(): void
    {
        $schema = Schema::connection($this->getConnection());

        if (! $schema->hasTable('passport_migration_created_tables')) {
            $schema->create('passport_migration_created_tables', function (Blueprint $table) {
                $table->string('migration')->primary();
            });
        }

        DB::connection($this->getConnection())->table('passport_migration_created_tables')->insert([
            'migration' => '2026_11_21_030907_create_oauth_device_codes_table',
        ]);
    }

    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return $this->connection ?? config('passport.connection');
    }
};
