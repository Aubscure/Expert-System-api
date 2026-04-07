<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            // Adding the new column after the ai_analysis column for clean ordering
            $table->string('essay_text', 2000)->nullable()->after('ai_analysis');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_sessions', function (Blueprint $table) {
            // Always provide a way to reverse the action
            $table->dropColumn('essay_text');
        });
    }
};
