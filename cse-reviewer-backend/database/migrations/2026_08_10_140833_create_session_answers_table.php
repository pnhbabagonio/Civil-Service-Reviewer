<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->string('question_id', 20);
            $table->char('user_answer', 1)->nullable();
            $table->char('correct_answer', 1);
            $table->boolean('is_correct');

            $table->foreign('question_id')
                  ->references('id')
                  ->on('questions')
                  ->onDelete('cascade');

            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_answers');
    }
};