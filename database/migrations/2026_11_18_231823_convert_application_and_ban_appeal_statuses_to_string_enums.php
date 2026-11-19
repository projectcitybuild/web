<?php

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
        $banAppealStatuses = [
            0 => 'pending',
            1 => 'accepted_unban',
            2 => 'accepted_tempban',
            3 => 'denied',
        ];
        $applicationStatuses = [
            1 => 'pending',
            2 => 'approved',
            3 => 'denied',
        ];

        $this->assertKnownStatuses('ban_appeals', array_keys($banAppealStatuses));
        $this->assertKnownStatuses('builder_rank_applications', array_keys($applicationStatuses));

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->string('status')->change();
        });

        foreach ($banAppealStatuses as $oldValue => $newValue) {
            DB::table('ban_appeals')->where('status', (string) $oldValue)->update(['status' => $newValue]);
        }

        foreach ($applicationStatuses as $oldValue => $newValue) {
            DB::table('builder_rank_applications')->where('status', (string) $oldValue)->update(['status' => $newValue]);
        }

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted_unban', 'accepted_tempban', 'denied'])->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'denied'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $banAppealStatuses = [
            'pending' => 0,
            'accepted_unban' => 1,
            'accepted_tempban' => 2,
            'denied' => 3,
        ];
        $applicationStatuses = [
            'pending' => 1,
            'approved' => 2,
            'denied' => 3,
        ];

        $this->assertKnownStatuses('ban_appeals', array_keys($banAppealStatuses));
        $this->assertKnownStatuses('builder_rank_applications', array_keys($applicationStatuses));

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->string('status')->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->string('status')->change();
        });

        foreach ($banAppealStatuses as $oldValue => $newValue) {
            DB::table('ban_appeals')->where('status', $oldValue)->update(['status' => $newValue]);
        }

        foreach ($applicationStatuses as $oldValue => $newValue) {
            DB::table('builder_rank_applications')->where('status', $oldValue)->update(['status' => $newValue]);
        }

        Schema::table('ban_appeals', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->change();
        });

        Schema::table('builder_rank_applications', function (Blueprint $table) {
            $table->integer('status')->change();
        });
    }

    /**
     * @param  array<int, int|string>  $allowedStatuses
     */
    private function assertKnownStatuses(string $table, array $allowedStatuses): void
    {
        $unexpectedStatuses = DB::table($table)
            ->whereNotIn('status', $allowedStatuses)
            ->distinct()
            ->pluck('status')
            ->all();

        if ($unexpectedStatuses !== []) {
            throw new RuntimeException(sprintf(
                'Cannot convert %s.status; unexpected values: %s',
                $table,
                json_encode($unexpectedStatuses, JSON_THROW_ON_ERROR),
            ));
        }
    }
};
