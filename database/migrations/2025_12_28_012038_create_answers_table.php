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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('message_id');
            $table->string('price');
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('respondent_id')->nullable();
            $table->string('respondent_profile_image_path')->nullable();
            $table->string('respondent_name')->nullable();
            $table->string('respondent_by_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
