<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Fortify;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign('servers_server_category_id_foreign');
            $table->dropColumn('server_category_id');
            $table->integer('display_order')->default(0)->change();
        });
        Schema::drop('server_categories');

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('discourse_name');
        });

        Schema::rename(from: 'groups_accounts', to: 'groups_accounts_pivot');

        Schema::table('accounts', function (Blueprint $table) {
            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->timestamp('two_factor_confirmed_at')
                ->after('two_factor_recovery_codes')
                ->nullable();

            $table->timestamp('email_verified_at')
                ->nullable()
                ->after('email');

            $table->timestamp('password_changed_at')
                ->nullable()
                ->after('last_login_at');
        });

        Schema::drop('personal_access_tokens');

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::drop('account_password_resets');
        Schema::drop('group_scopes_pivot');
        Schema::drop('group_scopes');
        Schema::drop('server_token_scopes_pivot');
        Schema::drop('server_token_scopes');
        Schema::drop('server_tokens');
        Schema::drop('pages');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::table('account_email_changes', function (Blueprint $table) {
            $table->dropColumn('is_previous_confirmed');
            $table->timestamp('expires_at')->after('is_new_confirmed');
            $table->renameColumn('is_new_confirmed', 'is_confirmed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // v4 - Point of no return
    }
};
