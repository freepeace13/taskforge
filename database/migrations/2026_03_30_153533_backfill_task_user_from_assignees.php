<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tasks')
            ->whereNotNull('assigned_to_user_id')
            ->orderBy('id')
            ->chunkById(100, function ($tasks): void {
                foreach ($tasks as $task) {
                    DB::table('task_user')->insertOrIgnore([
                        'task_id' => $task->id,
                        'user_id' => $task->assigned_to_user_id,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('task_user')->truncate();
    }
};
