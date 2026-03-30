<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Project::query()->orderBy('id')->chunkById(100, function ($projects): void {
            foreach ($projects as $project) {
                /** @var Project $project */
                $project->fillMissingIdentity();
                $project->backfillMissingTaskKeys();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
