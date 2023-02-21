<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDonations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('prev_rank_id');
        });
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('forum_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->integer('prev_rank_id')->unsigned()->nullable()->after('perks_end_at')->comment('ID of their previous group before becoming a donator');
            $table->integer('forum_user_id')->unsigned()->default(0)->after('account_id')->comment('ID of their forum account. To be replaced later when we switch to discord');
        });
    }
}
