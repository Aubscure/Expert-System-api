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
        Schema::create('severity_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')
                ->constrained('questionnaires')
                ->cascadeOnDelete();
            // e.g. "Minimal Depression", "Mild", "Moderate", "Severe"
            $table->string('label', 100);
            $table->unsignedSmallInteger('min_score');
            $table->unsignedSmallInteger('max_score');
            $table->string('description', 500)->nullable();
            // Color code for UI display (hex, 7 chars: #RRGGBB)
            $table->char('color_hex', 7)->nullable();
            $table->unsignedSmallInteger('order_index');
            $table->timestamps();

            // No two levels in the same questionnaire should have the same order
            $table->unique(['questionnaire_id', 'order_index']);

            $table->index('questionnaire_id');
        });
        // Ensure min < max at the database level
        DB::statement('ALTER TABLE severity_levels ADD CONSTRAINT chk_min_max_score CHECK (min_score <= max_score)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('severity_levels');
    }
};
