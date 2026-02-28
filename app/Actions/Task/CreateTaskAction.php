<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\CreatesTaskAction as CreatesTaskContract;
use App\Data\TaskData;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class CreateTaskAction implements CreatesTaskContract
{
    use AuthorizesActions;

    public function create(User $actor, Project $project, TaskData $data): Task
    {
        $this->authorizeForUser($actor, 'create', [Task::class, $project]);

        $task = $project->tasks()->create([
            'title' => $data->title,
            'description' => $data->description,
            'priority' => $data->priority,
            'due_date' => $data->dueDate
        ]);

        return $task;
    }
}
