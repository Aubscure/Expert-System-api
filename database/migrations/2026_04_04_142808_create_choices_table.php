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
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();
            $table->string('body', 255);
            // Score value: 0–10 typical for depression scales
            $table->unsignedTinyInteger('score_value');
            $table->unsignedSmallInteger('order_index');
            $table->timestamps();

            $table->unique(['question_id', 'order_index']);
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('choices');
    }
};
