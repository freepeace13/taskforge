<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\UpdatesTaskAction as UpdatesTaskContract;
use App\Data\TaskData;
use App\Models\Task;
use App\Models\User;

class UpdateTaskAction implements UpdatesTaskContract
{
    public function update(User $actor, Task $task, TaskData $data): Task
    {
        $project = $task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not belong to this organization.');
        }

        $task->update([
            'title' => $data->title,
            'description' => $data->description ?? $task->description,
            'priority' => $data->priority ?? $task->priority,
            'due_date' => $data->dueDate ?? $task->due_date,
        ]);


        return $task;
    }
}
