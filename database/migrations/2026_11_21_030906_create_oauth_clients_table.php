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

        if ($schema->hasTable('oauth_clients')) {
            return;
        }

        $schema->create('oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('owner');
            $table->string('name');
            $table->string('secret')->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect_uris');
            $table->text('grant_types');
            $table->boolean('revoked');
            $table->timestamps();
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

        if (! $createdTables->where('migration', '2026_11_21_030906_create_oauth_clients_table')->exists()) {
            if (! DB::connection($this->getConnection())->table('passport_migration_created_tables')->exists()) {
                $schema->drop('passport_migration_created_tables');
            }

            return;
        }

        $schema->dropIfExists('oauth_clients');
        DB::connection($this->getConnection())->table('passport_migration_created_tables')
            ->where('migration', '2026_11_21_030906_create_oauth_clients_table')
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
            'migration' => '2026_11_21_030906_create_oauth_clients_table',
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
