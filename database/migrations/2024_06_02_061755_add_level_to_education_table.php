<?php

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
        Schema::table('education', function (Blueprint $table) {
            $table->string('level')->after('user_id')->nullable(); // SD, SMP, SMA, S1, S2, S3
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            //
        });
    }
};
