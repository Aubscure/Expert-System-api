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
        Schema::create('expert_invitations', function (Blueprint $table) {
            $table->id();
            // UUID token — 36 chars for v4 UUID
            $table->char('token', 36)->unique();
            // Optional: admin pre-fills the invitee email for display purposes only
            $table->string('email', 150)->nullable();
            // Who generated this link
            $table->foreignId('created_by')->constrained('admins')->restrictOnDelete();
            $table->timestamp('expires_at');
            // Null = unused | timestamp = when it was used
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            // Index for fast token lookups on registration page
            $table->index(['token', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_invitations');
    }
};
