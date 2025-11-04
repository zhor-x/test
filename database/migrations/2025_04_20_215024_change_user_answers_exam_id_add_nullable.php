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
        Schema::table('user_answers_old', function (Blueprint $table) {
            $table->unsignedBigInteger('user_exam_id')->nullable()->change();
            $table->unsignedBigInteger('category_id')->nullable()->after('question_id');
            $table->unsignedBigInteger('user_id')->after('user_exam_id');
            $table->foreign('category_id')->references('id')->on('categories_old')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_exam_id')->change();
            $table->dropForeign(['category_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['category_id', 'user_id']);
        });
    }
};
