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
        PasswordReset::truncate();

        Schema::table('account_password_resets', function (Blueprint $table) {
            $table->integer('account_id')->unsigned();
            $table->dateTime('expires_at');
            $table->renameColumn('created_at', 'created_at_tmp');
            $table->bigIncrements('id')->first();
            $table->timestamps();

            $table->foreign('account_id')
                ->references('account_id')
                ->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
