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
        Schema::create('organizations', function ($table) {
            $table->id();

            // Multi-tenant key (human-friendly)
            $table->string('slug', 80)->unique();
            $table->string('name', 150);

            // Owner is a user (not necessarily the only admin)
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
