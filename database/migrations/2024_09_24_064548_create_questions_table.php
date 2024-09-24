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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [0, 1, 2]);
            $table->text('question_text')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->integer('difficulty')->default(1);
            $table->integer('focus_level')->nullable();
            $table->integer('average_time')->nullable();
            $table->float('correct_percentage')->nullable();
            $table->text('note')->nullable();
            $table->enum('is_active',[0,1]);
            $table->enum('is_demo',[0,1]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};