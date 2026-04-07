<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Required for raw SQL statements

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quiz_responses', function (Blueprint $table) {
            // 1. Drop the existing foreign key and unique constraint
            $table->dropForeign(['question_id']);
            $table->dropUnique(['quiz_session_id', 'question_id']);

            // 2. Redefine the column as nullable
            $table->foreignId('question_id')
                ->nullable()
                ->change()
                ->constrained('questions')
                ->restrictOnDelete();
        });

        // 3. Apply raw SQL partial indexes outside the Blueprint closure
        DB::statement('CREATE UNIQUE INDEX quiz_responses_session_question_unique
            ON quiz_responses (quiz_session_id, question_id)
            WHERE question_id IS NOT NULL');

        DB::statement('CREATE UNIQUE INDEX quiz_responses_session_essay_unique
            ON quiz_responses (quiz_session_id)
            WHERE question_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop the custom partial indexes first
        DB::statement('DROP INDEX IF EXISTS quiz_responses_session_question_unique');
        DB::statement('DROP INDEX IF EXISTS quiz_responses_session_essay_unique');

        Schema::table('quiz_responses', function (Blueprint $table) {
            // 2. Drop the nullable foreign key
            $table->dropForeign(['question_id']);

            // 3. Revert the column back to not null
            $table->foreignId('question_id')
                ->nullable(false)
                ->change()
                ->constrained('questions')
                ->restrictOnDelete();

            // 4. Restore the original unique constraint
            $table->unique(['quiz_session_id', 'question_id']);
        });
    }
};
