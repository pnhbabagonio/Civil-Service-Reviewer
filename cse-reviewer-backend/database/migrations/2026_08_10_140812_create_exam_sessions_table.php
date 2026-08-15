<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_token', 64)->nullable();
            $table->enum('mode', ['timed', 'practice', 'simulator']);
            $table->json('categories');
            $table->json('subcategory_keys')->nullable();
            $table->string('difficulty', 20)->default('all');
            $table->unsignedInteger('total_questions');
            $table->unsignedInteger('score');
            $table->decimal('percentage', 5, 2);
            $table->boolean('passed');
            $table->unsignedInteger('time_taken_sec');
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps(); // ← Add this line

            $table->index('user_id');
            $table->index('guest_token');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};