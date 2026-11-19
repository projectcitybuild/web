<?php

use App\Domains\BanAppeals\Data\BanAppealStatus;
use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->string('status')->change();
        });

        foreach ([
            0 => BanAppealStatus::PENDING->value,
            1 => BanAppealStatus::ACCEPTED_UNBAN->value,
            2 => BanAppealStatus::ACCEPTED_TEMPBAN->value,
            3 => BanAppealStatus::DENIED->value,
        ] as $oldValue => $newValue) {
            DB::table('ban_appeals')->where('status', $oldValue)->update(['status' => $newValue]);
        }

        foreach ([
            1 => ApplicationStatus::PENDING->value,
            2 => ApplicationStatus::APPROVED->value,
            3 => ApplicationStatus::DENIED->value,
        ] as $oldValue => $newValue) {
            DB::table('builder_rank_applications')->where('status', $oldValue)->update(['status' => $newValue]);
        }

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->enum('status', array_map(fn (BanAppealStatus $status): string => $status->value, BanAppealStatus::cases()))->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->enum('status', array_map(fn (ApplicationStatus $status): string => $status->value, ApplicationStatus::cases()))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->string('status')->change();
        });

        foreach ([
            'pending' => 0,
            'accepted_unban' => 1,
            'accepted_tempban' => 2,
            'denied' => 3,
        ] as $oldValue => $newValue) {
            DB::table('ban_appeals')->where('status', $oldValue)->update(['status' => $newValue]);
        }

        foreach ([
            'pending' => 1,
            'approved' => 2,
            'denied' => 3,
        ] as $oldValue => $newValue) {
            DB::table('builder_rank_applications')->where('status', $oldValue)->update(['status' => $newValue]);
        }

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->integer('status')->change();
        });
    }
};
