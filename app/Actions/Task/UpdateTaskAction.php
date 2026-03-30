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

        $status = $data->status ?? $task->status;
        $completedAt = $status === 'done'
            ? ($task->completed_at ?? now())
            : null;

        $task->update([
            'title' => $data->title,
            'description' => $data->description,
            'priority' => $data->priority,
            'due_date' => $data->dueDate,
            'status' => $status,
            'completed_at' => $completedAt,
        ]);

        if ($data->memberIds !== null) {
            $task->members()->sync($data->memberIds);
        }

        return $task;
    }
}
