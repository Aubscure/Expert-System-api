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
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expert_id')->constrained('experts')->restrictOnDelete();
            $table->string('title', 255);
            $table->string('description', 500)->nullable();
            // draft | published
            $table->enum('status', ['draft', 'published'])->default('draft');
            // Expert can toggle visibility even while published
            $table->boolean('is_visible')->default(false);
            // Require essay response at the end?
            $table->boolean('has_essay_question')->default(true);
            $table->string('essay_prompt', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_visible']); // used in public listing query
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
