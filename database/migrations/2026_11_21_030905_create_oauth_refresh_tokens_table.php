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

        if ($schema->hasTable('oauth_refresh_tokens')) {
            return;
        }

        $schema->create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->char('access_token_id', 80)->index();
            $table->boolean('revoked');
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

        if (! $createdTables->where('migration', '2026_11_21_030905_create_oauth_refresh_tokens_table')->exists()) {
            if (! DB::connection($this->getConnection())->table('passport_migration_created_tables')->exists()) {
                $schema->drop('passport_migration_created_tables');
            }

            return;
        }

        $schema->dropIfExists('oauth_refresh_tokens');
        DB::connection($this->getConnection())->table('passport_migration_created_tables')
            ->where('migration', '2026_11_21_030905_create_oauth_refresh_tokens_table')
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
            'migration' => '2026_11_21_030905_create_oauth_refresh_tokens_table',
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
