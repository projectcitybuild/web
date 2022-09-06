<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups_accounts', function (Blueprint $table) {
            $table->dropForeign('groups_accounts_account_id_foreign');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
        });
        Schema::table('account_email_changes', function (Blueprint $table) {
            $table->dropForeign('account_email_changes_account_id_foreign');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_email_changes', function (Blueprint $table) {
            $table->dropForeign('account_email_changes_account_id_foreign');
            $table->foreign('account_id')->references('account_id')->on('accounts');
        });
        Schema::table('groups_accounts', function (Blueprint $table) {
            $table->dropForeign('groups_accounts_account_id_foreign');
            $table->foreign('account_id')->references('account_id')->on('accounts');
        });
    }
};
