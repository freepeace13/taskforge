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
        Schema::create('organization_invites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();

            // Who sent the invite (optional but useful)
            $table->foreignId('invited_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('email', 190);
            $table->string('role', 20)->default('member');

            // Token used to accept invite (can be sent by email)
            $table->string('token', 64)->unique();

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'email']);
            $table->index(['organization_id', 'accepted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_invites');
    }
};
