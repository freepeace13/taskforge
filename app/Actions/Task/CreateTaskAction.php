<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\CreatesTaskAction as CreatesTaskContract;
use App\Data\TaskData;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class CreateTaskAction implements CreatesTaskContract
{
    public function create(User $actor, Project $project, TaskData $data): Task
    {
        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not belong to this organization.');
        }

        $task = $project->tasks()->create([
            'title' => $data->title,
            'description' => $data->description,
            'priority' => $data->priority,
            'due_date' => $data->dueDate
        ]);

        return $task;
    }
}
