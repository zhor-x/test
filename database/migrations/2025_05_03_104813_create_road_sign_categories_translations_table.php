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
        Schema::dropIfExists('road_sign_categories_translations');
        Schema::create('road_sign_categories_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('road_sign_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();

            $table->unique(
                ['road_sign_category_id', 'language_id'],
                'category_lang_unique' // 👈 ձեր կողմից տրված կարճ անուն
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('road_sign_categories_translations');
    }
};
