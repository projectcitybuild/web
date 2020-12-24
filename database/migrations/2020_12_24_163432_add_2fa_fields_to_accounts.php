<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add2faFieldsToAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('totp_secret')->nullable();
            $table->string('totp_backup_code')->nullable();
            $table->boolean('is_totp_enabled')->default(false);
            $table->integer('totp_last_used')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('totp_secret');
            $table->dropColumn('totp_backup_code');
            $table->dropColumn('is_totp_enabled');
            $table->dropColumn('totp_last_used');
        });
    }
}
