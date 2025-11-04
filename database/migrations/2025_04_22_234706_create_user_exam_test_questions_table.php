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
        Schema::create('user_exam_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_test_id')->constrained('user_exam_tests')->onDelete('cascade');
            $table->foreignId('test_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_answer_id')->constrained('answers')->onDelete('cascade');
            $table->boolean('is_right');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exam_test_questions');
    }
};
