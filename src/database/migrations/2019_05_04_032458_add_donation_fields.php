<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Entities\Donations\Models\Donation;

class AddDonationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->integer('perk_recipient_account_id')->unsigned();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_processed')->default(false);
            $table->boolean('is_countable')->default(true)->comment('Whether this donation should be used in calculating statistics/totals');
        });

        $donations = Donation::get();
        foreach ($donations as $donation) {
            $donation->perk_recipient_account_id = $donation->account_id;
            $donation->save();
        }

        Schema::table('donations', function (Blueprint $table) {
            $table->foreign('perk_recipient_account_id')->references('account_id')->on('accounts');
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
            $table->dropForeign('donations_perk_recipient_account_id_foreign');

            $table->dropColumn('perk_recipient_account_id');
            $table->dropColumn('is_processed');
            $table->dropColumn('is_anonymous');
            $table->dropColumn('is_countable');
        });
    }
}
