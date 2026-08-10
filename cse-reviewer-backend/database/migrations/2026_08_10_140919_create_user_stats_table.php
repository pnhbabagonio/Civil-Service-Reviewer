<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->onDelete('cascade');
            $table->string('username', 50);
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('total_correct')->default(0);
            $table->decimal('overall_accuracy', 5, 2)->default(0.00);
            $table->unsignedInteger('exams_taken')->default(0);
            $table->unsignedInteger('best_simulator_score')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('longest_streak')->default(0);
            $table->unsignedInteger('total_study_sec')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};