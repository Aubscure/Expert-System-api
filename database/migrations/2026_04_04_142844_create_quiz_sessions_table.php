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
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            // UUID stored in respondent's sessionStorage — the only "identifier"
            $table->uuid('uuid')->unique();
            $table->foreignId('questionnaire_id')
                ->constrained('questionnaires')
                ->restrictOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            // Computed after all responses submitted
            $table->unsignedSmallInteger('total_score')->nullable();
            $table->foreignId('severity_level_id')
                ->nullable()
                ->constrained('severity_levels')
                ->nullOnDelete();
            // AI narrative analysis — stored post-analysis
            // 2000 chars is ample for a paragraph-level analysis
            $table->string('ai_analysis', 2000)->nullable();
            $table->timestamps();

            $table->index('uuid');
            $table->index(['questionnaire_id', 'completed_at']); // for admin stats
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
    }
};
