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
        Schema::create('content_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('report_by_user_id')->unsigned()->index();
            $table->foreign('report_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('content_id')->unsigned()->index()->nullable();
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('set null');
            $table->string('topic'); // nudity, violence, scam, hoax, other
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_reports');
    }
};
