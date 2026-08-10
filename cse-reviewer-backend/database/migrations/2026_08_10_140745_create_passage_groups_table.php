<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passage_groups', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('category', 50);
            $table->string('subcategory', 100);
            $table->enum('difficulty', ['easy', 'average', 'difficult']);
            $table->string('title')->nullable();
            $table->text('passage_text');
            $table->string('source')->nullable();
            $table->boolean('has_image')->default(false);
            $table->timestamps();

            $table->index(['category', 'subcategory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passage_groups');
    }
};