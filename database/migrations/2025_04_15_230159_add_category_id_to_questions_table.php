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
        Schema::table('questions_old', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(); // Add category_id column

            // Add a foreign key constraint (optional)
            $table->foreign('category_id')->references('id')->on('categories_old')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
