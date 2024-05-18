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
        Schema::table('group_account_pivot', fn ($t) => $t->renameColumn('groups_accounts_id', 'id'));
        Schema::table('minecraft_auth_code', fn ($t) => $t->renameColumn('minecraft_auth_code_id', 'id'));
        Schema::table('payment', fn ($t) => $t->renameColumn('payment_id', 'id'));
        Schema::table('player', fn ($t) => $t->renameColumn('player_minecraft_id', 'id'));
        Schema::table('player_alias', fn ($t) => $t->renameColumn('players_alias_id', 'id'));


        // Fix weird default value

        Schema::table('ban_appeal', function (Blueprint $table) {
            $table->integer('decider_player_minecraft_id')
                ->nullable()
                ->unsigned()
                ->default(null)
                ->change();
        });


        // Fix weird foreign key column namings

        Schema::table('minecraft_auth_code', fn ($t) => $t->renameColumn('player_minecraft_id', 'player_id'));
        Schema::table('player_alias', fn ($t) => $t->renameColumn('player_minecraft_id', 'player_id'));
        Schema::table('ban_appeal', fn ($t) => $t->renameColumn('decider_player_minecraft_id', 'decider_player_id'));
        Schema::table('ban_appeal', fn ($t) => $t->renameColumn('game_ban_id', 'player_ban_id'));


        // Drop foreign keys

        Schema::table('account_balance_transaction', function (Blueprint $table) {
            $table->dropForeign('account_balance_transactions_account_id_foreign');
            $table->dropIndex('account_balance_transactions_account_id_foreign');
        });
        Schema::table('account_email_change', function (Blueprint $table) {
            $table->dropForeign('account_email_changes_account_id_foreign');
            $table->dropIndex('account_email_changes_account_id_foreign');
        });
        Schema::table('ban_appeal', function (Blueprint $table) {
            $table->dropForeign('ban_appeals_game_ban_id_foreign');
            $table->dropIndex('ban_appeals_game_ban_id_foreign');

            $table->dropForeign('ban_appeals_decider_player_minecraft_id_foreign');
            $table->dropIndex('ban_appeals_decider_player_minecraft_id_foreign');
        });
        Schema::table('donation', function (Blueprint $table) {
            $table->dropForeign('donations_account_id_foreign');
            $table->dropIndex('donations_account_id_foreign');
        });
        Schema::table('donation_perk', function (Blueprint $table) {
            $table->dropForeign('donation_perks_account_id_foreign');
            $table->dropIndex('donation_perks_account_id_foreign');

            $table->dropForeign('donation_perks_donation_id_foreign');
            $table->dropIndex('donation_perks_donation_id_foreign');

            $table->dropForeign('donation_perks_donation_tier_id_foreign');
            $table->dropIndex('donation_perks_donation_tier_id_foreign');
        });
        Schema::table('group_account_pivot', function (Blueprint $table) {
            $table->dropForeign('groups_accounts_group_id_foreign');
            $table->dropIndex('groups_accounts_group_id_foreign');

            $table->dropForeign('groups_accounts_account_id_foreign');
            $table->dropIndex('groups_accounts_account_id_foreign');
        });
        Schema::table('ip_ban', function (Blueprint $table) {
            $table->dropForeign('game_ip_bans_unbanner_player_id_foreign');
            $table->dropIndex('game_ip_bans_unbanner_player_id_foreign');
        });
        Schema::table('payment', function (Blueprint $table) {
            $table->dropForeign('payments_account_id_foreign');
            $table->dropIndex('payments_account_id_foreign');
        });
        Schema::table('player', function (Blueprint $table) {
            $table->dropForeign('players_minecraft_account_id_foreign');
            $table->dropIndex('players_minecraft_account_id_foreign');
        });
        Schema::table('player_alias', function (Blueprint $table) {
            $table->dropForeign('players_minecraft_aliases_player_minecraft_id_foreign');
            $table->dropIndex('players_minecraft_aliases_player_minecraft_id_foreign');
        });
        Schema::table('player_ban', function (Blueprint $table) {
            $table->dropForeign('game_network_bans_unbanner_player_id_foreign');
            $table->dropIndex('game_network_bans_unbanner_player_id_foreign');
        });
        Schema::table('stripe_product', function (Blueprint $table) {
            $table->dropForeign('stripe_products_donation_tier_id_foreign');
            $table->dropIndex('stripe_products_donation_tier_id_foreign');
        });


        // Convert old id columns to bigint (the new default of `id()`)

        Schema::table('account', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
        });
        Schema::table('account_balance_transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('account_email_change', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('badge', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
        });
        Schema::table('badge_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('account_id')->change();
            $table->unsignedBigInteger('badge_id')->change();
        });
        Schema::table('ban_appeal', function (Blueprint $table) {
            $table->unsignedBigInteger('player_ban_id')->change();
            $table->unsignedBigInteger('decider_player_id')->nullable()->change();
        });
        Schema::table('builder_rank_application', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('donation', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('donation_perk', function (Blueprint $table) {
            $table->unsignedBigInteger('donation_id')->change();
            $table->unsignedBigInteger('donation_tier_id')->change();
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('donation_tier', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
        });
        Schema::table('group', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
        });
        Schema::table('group_account_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('group_id')->change();
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('ip_ban', function (Blueprint $table) {
            $table->unsignedBigInteger('banner_player_id')->change();
            $table->unsignedBigInteger('unbanner_player_id')->nullable()->change();
        });
        Schema::table('minecraft_auth_code', function (Blueprint $table) {
            $table->unsignedBigInteger('player_id')->change();
        });
        Schema::table('payment', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->change();
        });
        Schema::table('player', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('account_id')->nullable()->change();
        });
        Schema::table('player_alias', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('player_id')->change();
        });
        Schema::table('player_ban', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('banned_player_id')->change();
            $table->unsignedBigInteger('banner_player_id')->nullable()->change();
            $table->unsignedBigInteger('unbanner_player_id')->nullable()->change();
        });
        Schema::table('player_warning', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
            $table->unsignedBigInteger('warned_player_id')->change();
            $table->unsignedBigInteger('warner_player_id')->nullable()->change();
        });
        Schema::table('stripe_product', function (Blueprint $table) {
            $table->unsignedBigInteger('donation_tier_id')->change();
        });

        // Restore foreign keys

        Schema::table('account_balance_transaction', function (Blueprint $table) {
            $table->foreignId('account_id')->change();
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('account_email_change', function (Blueprint $table) {
            $table->foreignId('account_id')->change();
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('badge_pivot', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('badge_id')->references('id')->on('badge');
        });
        Schema::table('ban_appeal', function (Blueprint $table) {
            $table->foreign('player_ban_id')->references('id')->on('player_ban');
            $table->foreign('decider_player_id')->references('id')->on('player');
        });
        Schema::table('builder_rank_application', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('donation', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('donation_perk', function (Blueprint $table) {
            $table->foreign('donation_id')->references('id')->on('donation');
            $table->foreign('donation_tier_id')->references('id')->on('donation_tier');
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('group_account_pivot', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('group');
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('ip_ban', function (Blueprint $table) {
            $table->foreign('banner_player_id')->references('id')->on('player');
            $table->foreign('unbanner_player_id')->references('id')->on('player');
        });
        Schema::table('minecraft_auth_code', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('player');
        });
        Schema::table('payment', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('player', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('account');
        });
        Schema::table('player_alias', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('player');
        });
        Schema::table('player_ban', function (Blueprint $table) {
            $table->foreign('banned_player_id')->references('id')->on('player');
            $table->foreign('banner_player_id')->references('id')->on('player');
            $table->foreign('unbanner_player_id')->references('id')->on('player');
        });
        Schema::table('player_warning', function (Blueprint $table) {
            $table->foreign('warned_player_id')->references('id')->on('player');
            $table->foreign('warner_player_id')->references('id')->on('player');
        });
        Schema::table('stripe_product', function (Blueprint $table) {
            $table->foreign('donation_tier_id')->references('id')->on('donation_tier');
        });


        // Introduce attributes

        Schema::create('attribute', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('group_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained(table: 'group');
            $table->foreignId('attribute_id')->constrained(table: 'attribute');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('perk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained(table: 'player');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();
        });

        Schema::create('perk_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained(table: 'group');
            $table->foreignId('attribute_id')->constrained(table: 'attribute');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Point of no return
    }
};
