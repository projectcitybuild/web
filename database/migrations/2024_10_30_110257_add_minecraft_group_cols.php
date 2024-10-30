<?php

use App\Models\DonationTier;
use App\Models\Group;
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
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('discourse_name');
            $table->integer('display_priority')->nullable()->after('minecraft_name');
            $table->string('minecraft_hover_text')->after('minecraft_name');
            $table->string('minecraft_display_name')->after('minecraft_name');
            $table->string('group_type')->nullable();
        });

        Schema::table('donation_perks', function (Blueprint $table) {
            $table->dropColumn('last_currency_reward_at');
        });

        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->dropColumn('currency_reward');
            $table->unsignedInteger('group_id')->nullable();

            $table->foreign('group_id')->references('group_id')->on('groups');
        });

        $group = Group::first();
        if ($group === null) {
            $group = Group::factory()->create();
        }
        DonationTier::get()->each(function ($tier) use($group) {
           $tier->group_id = $group->getKey();
           $tier->save();
        });

        Schema::table('donation_tiers', function (Blueprint $table) {
            $table->unsignedInteger('group_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
