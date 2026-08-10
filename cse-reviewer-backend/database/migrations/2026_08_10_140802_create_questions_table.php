<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('category', 50);
            $table->string('subcategory', 100);
            $table->enum('difficulty', ['easy', 'average', 'difficult']);
            $table->text('question_text');
            $table->json('choices');
            $table->char('answer', 1);
            $table->text('explanation')->nullable();
            $table->string('source')->nullable();
            $table->boolean('has_image')->default(false);
            $table->string('passage_group_id', 20)->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->foreign('passage_group_id')
                  ->references('id')
                  ->on('passage_groups')
                  ->onDelete('set null');

            $table->index('category');
            $table->index(['category', 'subcategory']);
            $table->index('difficulty');
            $table->index('version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};