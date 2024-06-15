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
        Schema::create('training_center_courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('training_center_id')->unsigned()->index();
            $table->foreign('training_center_id')->references('id')->on('training_centers')->onDelete('cascade');
            $table->string('title');
            $table->longText('description');
            $table->string('cover');
            $table->boolean('is_online');
            $table->boolean('is_certified');
            $table->integer('lessons_count');
            $table->string('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_center_courses');
    }
};
