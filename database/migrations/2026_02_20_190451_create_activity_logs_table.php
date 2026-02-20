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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();

            // who did it (nullable for system events)
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Optional scoping helpers (fast filters without polymorphic joins)
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();

            // Generic event info
            $table->string('event', 80); // e.g. task.completed, task.assigned, project.archived
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'created_at']);
            $table->index(['project_id', 'created_at']);
            $table->index(['task_id', 'created_at']);
            $table->index(['event']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
