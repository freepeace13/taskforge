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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            // Who is assigned (must also be org member; enforce via app logic)
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title', 200);
            $table->text('description')->nullable();

            // todo / in_progress / done
            $table->string('status', 30)->default('todo');

            // low / medium / high
            $table->string('priority', 20)->default('medium');

            $table->date('due_date')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'priority']);
            $table->index(['assigned_to_user_id']);
            $table->index(['completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
