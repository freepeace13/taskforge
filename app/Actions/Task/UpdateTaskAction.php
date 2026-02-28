<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\UpdatesTaskAction as UpdatesTaskContract;
use App\Data\TaskData;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class UpdateTaskAction implements UpdatesTaskContract
{
    use AuthorizesActions;

    public function update(User $actor, Task $task, TaskData $data): Task
    {
        $this->authorizeForUser($actor, 'update', $task);

        $task->update([
            'title' => $data->title,
            'description' => $data->description ?? $task->description,
            'priority' => $data->priority ?? $task->priority,
            'due_date' => $data->dueDate ?? $task->due_date,
        ]);


        return $task;
    }
}
