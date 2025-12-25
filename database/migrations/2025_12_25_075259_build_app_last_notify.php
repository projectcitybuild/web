<?php

use App\Models\BuilderRankApplication;
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
        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->dateTime('next_reminder_at')->nullable()->after('closed_at');
        });

        $openApps = BuilderRankApplication::where('closed_at', null)->get();
        foreach ($openApps as $app) {
            $app->next_reminder_at = $app->created_at->copy()->addDays(7);
            $app->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->dropColumn('next_reminder_at');
        });
    }
};
