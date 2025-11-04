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
        Schema::create('user_answers_old', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_exam_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('selected_option_id');
            $table->boolean('is_correct');
            $table->timestamps();

            $table->foreign('user_exam_id')->references('id')->on('user_exams_old')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions_old')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('question_options_old')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
