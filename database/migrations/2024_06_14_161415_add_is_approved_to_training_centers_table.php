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
        Schema::table('training_centers', function (Blueprint $table) {
            $table->string('country', 355)->after('phone');
            $table->string('website')->nullable()->after('phone');
            $table->boolean('is_approved')->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_centers', function (Blueprint $table) {
            //
        });
    }
};
