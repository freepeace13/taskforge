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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();

            $table->string('name', 160);
            $table->text('description')->nullable();

            // archive/restore endpoints
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'archived_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
