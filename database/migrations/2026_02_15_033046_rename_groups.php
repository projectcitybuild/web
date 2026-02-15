<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('groups', 'roles');

        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('group_id', 'id');
            $table->renameColumn('group_type', 'role_type');
        });

        Schema::rename('groups_accounts', 'roles_accounts');

        Schema::table('roles_accounts', function (Blueprint $table) {
            $table->renameColumn('groups_accounts_id', 'id');
            $table->renameColumn('group_id', 'role_id');
        });

        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->renameColumn('group_id', 'role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('roles', 'groups');

        Schema::table('groups', function (Blueprint $table) {
            $table->renameColumn('id', 'group_id');
            $table->renameColumn('role_type', 'group_type');
        });

        Schema::rename('roles_accounts', 'groups_accounts');

        Schema::table('groups_accounts', function (Blueprint $table) {
            $table->renameColumn('id', 'groups_accounts_id');
            $table->renameColumn('role_id', 'group_id');
        });

        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->renameColumn('role_id', 'group_id');
        });
    }
};
