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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')
                ->constrained('questionnaires')
                ->cascadeOnDelete(); // questions die with questionnaire
            // Question text — 500 chars max prevents abuse
            $table->string('body', 500);
            // Determines display order
            $table->unsignedSmallInteger('order_index');
            $table->timestamps();

            $table->unique(['questionnaire_id', 'order_index']); // no duplicate positions
            $table->index('questionnaire_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
