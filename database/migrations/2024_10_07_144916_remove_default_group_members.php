<?php

use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultGroup = Group::where('is_default', true)->first();

        // Delete all members from the default group
        DB::table('groups_accounts')
            ->where('group_id', $defaultGroup->getKey())
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Point of no return
    }
};
