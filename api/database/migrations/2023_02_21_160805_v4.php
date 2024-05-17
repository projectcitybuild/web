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

            $table->dropColumn('is_totp_enabled');
            $table->dropColumn('totp_last_used');
            $table->dropColumn('totp_backup_code');
            $table->dropColumn('totp_secret');
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

        Schema::table('game_player_bans', function (Blueprint $table) {
            $table->dropColumn('server_id');
            $table->rename('player_bans');
            $table->text('unban_reason')->after('unban_type')->nullable();
        });

        Schema::table('game_ip_bans', function (Blueprint $table) {
            $table->rename('ip_bans');
        });

        Schema::table('player_bans', function (Blueprint $table) {
            $table->json('meta')->after('reason')->nullable();
        });

        Schema::table('ip_bans', function (Blueprint $table) {
            $table->json('meta')->after('reason')->nullable();
        });

        Schema::table('players_minecraft', function (Blueprint $table) {
            $table->rename('players');
        });

        Schema::table('players_minecraft_aliases', function (Blueprint $table) {
            $table->renameColumn('players_minecraft_alias_id', 'players_alias_id');
            $table->rename('players_aliases');
        });

        Schema::table('donation_perks', function (Blueprint $table) {
            $table->dropColumn('last_currency_reward_at');
            $table->dropColumn('is_active');
            $table->integer('donation_id')->unsigned()->nullable()->change();
        });


        // Rename all tables to be singular

        Schema::table('accounts', fn ($t) => $t->rename('account'));
        Schema::table('account_balance_transactions', fn ($t) => $t->rename('account_balance_transaction'));
        Schema::table('account_email_changes', fn ($t) => $t->rename('account_email_change'));
        Schema::table('badges', fn ($t) => $t->rename('badge'));
        Schema::table('badges_pivot', fn ($t) => $t->rename('badge_pivot'));
        Schema::table('ban_appeals', fn ($t) => $t->rename('ban_appeal'));
        Schema::table('builder_rank_applications', fn ($t) => $t->rename('builder_rank_application'));
        Schema::table('donations', fn ($t) => $t->rename('donation'));
        Schema::table('donation_perks', fn ($t) => $t->rename('donation_perk'));
        Schema::table('donation_tiers', fn ($t) => $t->rename('donation_tier'));
        Schema::table('groups', fn ($t) => $t->rename('group'));
        Schema::table('groups_accounts_pivot', fn ($t) => $t->rename('group_account_pivot'));
        Schema::table('ip_bans', fn ($t) => $t->rename('ip_ban'));
        Schema::table('minecraft_auth_codes', fn ($t) => $t->rename('minecraft_auth_code'));
        Schema::table('payments', fn ($t) => $t->rename('payment'));
        Schema::table('players', fn ($t) => $t->rename('player'));
        Schema::table('players_aliases', fn ($t) => $t->rename('player_alias'));
        Schema::table('player_bans', fn ($t) => $t->rename('player_ban'));
        Schema::table('player_warnings', fn ($t) => $t->rename('player_warning'));
        Schema::table('servers', fn ($t) => $t->rename('server'));
        Schema::table('showcase_warps', fn ($t) => $t->rename('showcase_warp'));
        Schema::table('stripe_products', fn ($t) => $t->rename('stripe_product'));


        // Rename primary key columns

        Schema::table('account', fn ($t) => $t->renameColumn('account_id', 'id'));
        Schema::table('account_balance_transaction', fn ($t) => $t->renameColumn('balance_transaction_id', 'id'));
        Schema::table('account_email_change', fn ($t) => $t->renameColumn('account_email_change_id', 'id'));
        Schema::table('donation', fn ($t) => $t->renameColumn('donation_id', 'id'));
        Schema::table('donation_perk', fn ($t) => $t->renameColumn('donation_perks_id', 'id'));
        Schema::table('donation_tier', fn ($t) => $t->renameColumn('donation_tier_id', 'id'));
        Schema::table('group', fn ($t) => $t->renameColumn('group_id', 'id'));
        Schema::table('minecraft_auth_code', fn ($t) => $t->renameColumn('minecraft_auth_code_id', 'id'));
        Schema::table('payment', fn ($t) => $t->renameColumn('payment_id', 'id'));
        Schema::table('player', fn ($t) => $t->renameColumn('player_minecraft_id', 'id'));
        Schema::table('player_alias', fn ($t) => $t->renameColumn('players_alias_id', 'id'));


        // Fix weird foreign key column namings

        Schema::table('minecraft_auth_code', fn ($t) => $t->renameColumn('player_minecraft_id', 'player_id'));
        Schema::table('player_alias', fn ($t) => $t->renameColumn('player_minecraft_id', 'player_id'));

        // Introduce attributes

//        Schema::create('attribute', function (Blueprint $table) {
//            $table->id();
//            $table->string('name');
//        });
//
//        Schema::create('group_attribute', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('group_id')->constrained(
//                table: 'group',
//                column: 'group_id',
//            );
//            $table->foreignId('attribute_id')->constrained(
//                table: 'attribute',
//            );
//            $table->string('value');
//            $table->timestamps();
//        });
//
//        Schema::create('perk', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('player_id')->constrained(
//                table: 'players',
//                column: 'player_minecraft_id',
//            );
//            $table->dateTime('starts_at');
//            $table->dateTime('ends_at');
//            $table->timestamps();
//        });
//
//        Schema::create('perk_attribute', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('group_id')->constrained(
//                table: 'groups',
//                column: 'group_id',
//            );
//            $table->foreignId('attribute_id')->constrained(
//                table: 'attributes',
//            );
//            $table->string('value');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Point of no return
    }
};
