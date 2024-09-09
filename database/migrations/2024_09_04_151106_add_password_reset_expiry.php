<?php

use App\Models\PasswordReset;
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
        Schema::table('account_password_resets', function (Blueprint $table) {
            $table->dateTime('expires_at')->nullable();
            $table->renameColumn('created_at', 'created_at_tmp');
            $table->bigIncrements('id')->nullable()->first();
        });

        $i = 1;
        $resets = PasswordReset::all();
        foreach ($resets as $reset) {
            $reset->expires_at = $reset->created_at?->addDay() ?? now();
            $reset->id = $i;
            $reset->save();
            $i++;
        }

        Schema::table('account_password_resets', function (Blueprint $table) {
            $table->dateTime('expires_at')->change();
            $table->timestamps();
        });

        $resets = PasswordReset::all();
        foreach ($resets as $reset) {
            $reset->created_at = $reset->created_at_tmp;
            $reset->updated_at = $reset->created_at_tmp;
            $reset->save();
        }

        Schema::table('account_password_resets', function (Blueprint $table) {
            $table->dropColumn('created_at_tmp');
            $table->bigIncrements('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
