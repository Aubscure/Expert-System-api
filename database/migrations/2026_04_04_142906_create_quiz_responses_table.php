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
        Schema::create('quiz_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_session_id')
                ->constrained('quiz_sessions')
                ->cascadeOnDelete();
            $table->foreignId('question_id')
                ->constrained('questions')
                ->restrictOnDelete();
            // NULL for essay-type response
            $table->foreignId('choice_id')
                ->nullable()
                ->constrained('choices')
                ->nullOnDelete();
            // Essay text — NOT stored long-term. See Privacy Architecture section.
            // This is populated temporarily during submission and cleared after AI analysis.
            // 2000 chars max prevents memory abuse
            $table->string('essay_text', 2000)->nullable();
            $table->timestamps();

            // One response per question per session
            $table->unique(['quiz_session_id', 'question_id']);
            $table->index('quiz_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_responses');
    }
};
